<?php

namespace App\Http\Controllers;

use App\CollectionItem;
use App\Ejaculation;
use App\Parser\SearchQuery\InvalidExpressionException;
use App\Parser\SearchQueryParser;
use App\Tag;
use App\Utilities\Formatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /** @var Formatter */
    private $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function index(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required'
        ]);

        $results = Ejaculation::query()
            ->where(function (Builder $query) {
                $query->where('is_private', false)->whereHas('user', fn ($q) => $q->where('is_protected', false));
                if (Auth::check()) {
                    $query->orWhereHas('user', fn ($q) => $q->where('id', Auth::id()));
                }
            })
            ->orderBy('ejaculated_date', 'desc')
            ->with(['user', 'tags'])
            ->withLikes()
            ->withMutedStatus();

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
                        case 'date':
                            $op = $expression->negative ? '<>' : '=';
                            $results = $results->whereDate('ejaculated_date', $op, $expression->keyword);
                            break;
                        case 'since':
                            $op = $expression->negative ? '<' : '>=';
                            $results = $results->whereDate('ejaculated_date', $op, $expression->keyword);
                            break;
                        case 'until':
                            $op = $expression->negative ? '>' : '<=';
                            $results = $results->whereDate('ejaculated_date', $op, $expression->keyword);
                            break;
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
                            $results = $results->whereHas('user', function ($query) use ($expression) {
                                $query->where('name', '=', $expression->keyword);
                            }, $op);
                            break;
                        case 'is':
                            switch ($expression->keyword) {
                                case 'sensitive':
                                    $results = $results->where('is_too_sensitive', !$expression->negative);
                                    break;
                            }
                            break;
                        case 'has':
                            switch ($expression->keyword) {
                                case 'link':
                                case 'url':
                                    $op = $expression->negative ? '=' : '<>';
                                    $results = $results->where('link', $op, '');
                                    break;
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

        $results = $results->paginate(20)->appends($inputs);

        return view('search.index')->with(compact('inputs', 'results'));
    }

    public function collection(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required'
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

        $results = $results->paginate(20)->appends($inputs);

        return view('search.collection')->with(compact('inputs', 'results'));
    }

    public function relatedTag(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required'
        ]);

        $q = $this->normalizeQuery($inputs['q']);
        $results = Tag::query()
            ->where('normalized_name', 'like', "%{$q}%")
            ->paginate(50)
            ->appends($inputs);

        return view('search.relatedTag')->with(compact('inputs', 'results'));
    }

    private function normalizeQuery(string $query): string
    {
        return $this->formatter->normalizeTagName($query);
    }
}
