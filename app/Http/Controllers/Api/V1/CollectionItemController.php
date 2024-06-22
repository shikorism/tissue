<?php

namespace App\Http\Controllers\Api\V1;

use App\Collection;
use App\CollectionItem;
use App\Events\LinkDiscovered;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionItemResource;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
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

    public function store(Request $request, Collection $collection)
    {
        $this->authorize('edit', $collection);

        $validated = Validator::make($request->input(), [
            'link' => [
                'required',
                'url',
                'max:2000',
                Rule::unique('collection_items', 'link')->where(function ($query) use ($collection) {
                    return $query->where('collection_id', $collection->id);
                })
            ],
            'note' => 'nullable|string|max:500',
            'tags' => 'nullable|array|max:40',
            'tags.*' => ['string', 'not_regex:/[\s\r\n]/u', 'max:255'],
        ], [
            'tags.max' => 'タグは最大40個までです。',
            'tags.*.not_regex' => 'The :attribute cannot contain spaces, tabs and newlines.',
        ])->validate();

        if ($collection->items()->count() >= CollectionItem::PER_COLLECTION_LIMIT) {
            throw new UnprocessableEntityHttpException('これ以上コレクションに追加することはできません');
        }

        $item = DB::transaction(function () use ($collection, $validated) {
            $item = new CollectionItem($validated);
            $collection->items()->save($item);
            $collection->touch();

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

            return $item;
        });

        event(new LinkDiscovered($item->link));

        // 登録フォーム用の処理
        if ($request->input('flash') === true) {
            session()->flash('status', '登録しました。');
        }

        return new CollectionItemResource($item);
    }

    public function update(Request $request, Collection $collection, CollectionItem $item)
    {
        $this->authorize('edit', $item);

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
            $item->collection->touch();

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
        $this->authorize('edit', $item);

        DB::transaction(function () use ($item) {
            $item->collection->touch();
            $item->delete();
        });

        return response()->noContent();
    }
}
