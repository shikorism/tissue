<?php

namespace App\Http\Controllers\Api;

use App\Collection;
use App\CollectionItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionItemResource;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;

class CollectionItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $collection = $request->route('collection');
            if ($collection instanceof Collection && !$collection->user->isMe()) {
                if ($collection->is_private || $collection->user->is_protected) {
                    throw new NotFoundHttpException();
                }
            }

            return $next($request);
        });
    }

    public function index(Request $request, Collection $collection)
    {
        $inputs = $request->validate([
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $items = $collection->items()
            ->orderByDesc('id')
            ->paginate($inputs['per_page'] ?? 20);

        return response()->fromPaginator($items, CollectionItemResource::class);
    }

    public function update(Request $request, Collection $collection, CollectionItem $item)
    {
        $this->authorize('update', $item);

        $validated = Validator::make($request->input(), [
            'note' => 'nullable|string|max:500',
            'tags' => 'nullable|array|max:40',
            'tags.*' => ['string', 'not_regex:/[\s\r\n]/u', 'max:255'],
        ], [
            'tags.max' => 'タグは最大40個までです。',
            'tags.*.not_regex' => 'The :attribute cannot contain spaces, tabs and newlines.',
        ])->validate();

        if (isset($validated['note'])) {
            $item->note = $validated['note'];
        }

        DB::transaction(function () use ($item, $validated) {
            $item->save();

            if (isset($validated['tags'])) {
                $tagIds = [];
                if (!empty($validated['tags'])) {
                    foreach ($validated['tags'] as $tag) {
                        $tag = trim($tag);
                        if ($tag === '') {
                            continue;
                        }

                        $tag = Tag::firstOrCreate(['name' => $tag]);
                        $tagIds[] = $tag->id;
                    }
                }
                $item->tags()->sync($tagIds);
            }
        });

        return new CollectionItemResource($item);
    }

    public function destroy(Collection $collection, CollectionItem $item)
    {
        $this->authorize('destroy', $item);
        $item->delete();

        return response()->noContent();
    }
}
