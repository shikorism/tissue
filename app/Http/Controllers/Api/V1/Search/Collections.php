<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\CollectionItem;
use App\Http\Resources\CollectionItemResource;
use App\Parser\SearchQuery\InvalidExpressionException;
use App\Parser\SearchQueryParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Collections extends BaseController
{
    public function __invoke(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required',
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $results = CollectionItem::query()
            ->select('collection_items.*')
            ->join('collections', 'collections.id', '=', 'collection_items.collection_id')
            ->with(['collection', 'collection.user', 'tags'])
            ->where(function (Builder $query) {
                $query->where('collections.is_private', false)->whereHas('collection.user', fn (Builder $q) => $q->where('is_protected', false));
                if (Auth::check()) {
                    $query->orWhereHas('collection.user', fn (Builder $q) => $q->where('id', Auth::id()));
                }
            })
            ->orderByDesc('id');

        try {
            $parser = (new SearchQueryParser())->parse($inputs['q']);
            foreach ($parser->getExpressions() as $expression) {
                // fuzzy?
                if ($expression->target === null) {
                    if (preg_match('/^https?:/', $expression->keyword)) {
                        $results = $results->where(function ($query) use ($expression) {
                            if ($expression->negative) {
                                $query->where('link', 'not like', $this->formatter->makePartialMatch($expression->keyword))
                                    ->where('note', 'not like', $this->formatter->makePartialMatch($expression->keyword));
                            } else {
                                $query->where('link', 'like', $this->formatter->makePartialMatch($expression->keyword))
                                    ->orWhere('note', 'like', $this->formatter->makePartialMatch($expression->keyword));
                            }
                        });
                    } else {
                        $op = $expression->negative ? '<' : '>=';
                        $results = $results->whereHas('tags', function ($query) use ($expression) {
                            $query->where('normalized_name', 'like', $this->formatter->makePartialMatch($this->normalizeQuery($expression->keyword)));
                        }, $op);
                    }
                } else {
                    switch ($expression->target) {
                        case 'link':
                        case 'url':
                            $op = $expression->negative ? 'not like' : 'like';
                            $results = $results->where('link', $op, $this->formatter->makePartialMatch($expression->keyword));
                            break;
                        case 'note':
                            $op = $expression->negative ? 'not like' : 'like';
                            $results = $results->where('note', $op, $this->formatter->makePartialMatch($expression->keyword));
                            break;
                        case 'tag':
                            $op = $expression->negative ? '<' : '>=';
                            $results = $results->whereHas('tags', function ($query) use ($expression) {
                                $query->where('normalized_name', '=', $this->normalizeQuery($expression->keyword));
                            }, $op);
                            break;
                        case 'user':
                            $op = $expression->negative ? '<' : '>=';
                            $results = $results->whereHas('collection.user', function ($query) use ($expression) {
                                $query->where('name', '=', $expression->keyword);
                            }, $op);
                            break;
                        case 'has':
                            switch ($expression->keyword) {
                                case 'note':
                                    $op = $expression->negative ? '=' : '<>';
                                    $results = $results->where('note', $op, '');
                                    break;
                            }
                            break;
                    }
                }
            }
        } catch (InvalidExpressionException $e) {
            $results = DB::query()->selectRaw('1')->whereRaw('false');
        }

        $results = $results->paginate($inputs['per_page'] ?? 20)->appends($inputs);

        return response()->fromPaginator($results, CollectionItemResource::class);
    }
}
