<?php

namespace App\Http\Controllers\Api\V1;

use App\Collection;
use App\CollectionItem;
use App\Events\LinkDiscovered;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CollectionController extends Controller
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

    public function index()
    {
        $collections = Auth::user()->collections();

        return response()->json($collections->get()->map(fn ($collection) => new CollectionResource($collection)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('collections', 'title')->where(function ($query) {
                    return $query->where('user_id', Auth::user()->id);
                }),
            ],
            'is_private' => 'required|boolean',
            'items' => 'nullable|array|max:100',
            'items.*.link' => 'required|url|max:2000',
            'items.*.tags' => 'nullable|array|max:40',
            'items.*.tags.*' => ['string', 'not_regex:/[\s\r\n]/u', 'max:255'],
        ]);

        if (Auth::user()->collections()->count() >= Collection::PER_USER_LIMIT) {
            throw new UnprocessableEntityHttpException('これ以上コレクションを作成することはできません');
        }

        [$collection, $collectionItems] = DB::transaction(function () use ($validated) {
            $collection = new Collection(Arr::except($validated, 'items'));
            Auth::user()->collections()->save($collection);

            $collectionItems = [];
            if (!empty($validated['items'])) {
                $items = array_unique($validated['items']);
                foreach ($items as $attributes) {
                    $item = new CollectionItem($attributes);
                    $collection->items()->save($item);

                    $tagIds = [];
                    if (!empty($attributes['tags'])) {
                        foreach ($attributes['tags'] as $tag) {
                            $tag = trim($tag);
                            if ($tag === '') {
                                continue;
                            }

                            $tag = Tag::firstOrCreate(['name' => $tag]);
                            $tagIds[] = $tag->id;
                        }
                    }
                    $item->tags()->sync($tagIds);
                    $collectionItems[] = $item;
                }
            }

            return [$collection, $collectionItems];
        });

        foreach ($collectionItems as $item) {
            event(new LinkDiscovered($item->link));
        }

        return new CollectionResource($collection);
    }

    public function show(Collection $collection)
    {
        return new CollectionResource($collection);
    }

    public function update(Request $request, Collection $collection)
    {
        $this->authorize('edit', $collection);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('collections', 'title')->where(function ($query) use ($collection) {
                    return $query->where('user_id', $collection->user_id)->where('id', '<>', $collection->id);
                }),
            ],
            'is_private' => 'required|boolean',
        ]);

        $collection->fill($validated);
        $collection->save();

        return new CollectionResource($collection);
    }

    public function destroy(Collection $collection)
    {
        $this->authorize('edit', $collection);
        $collection->delete();

        return response()->noContent();
    }
}
