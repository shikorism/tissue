<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Ejaculation;
use App\Http\Resources\EjaculationResource;
use App\Parser\SearchQuery\InvalidExpressionException;
use App\Parser\SearchQueryParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Checkins extends BaseController
{
    public function __invoke(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required',
            'per_page' => 'nullable|integer|between:10,100',
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

        $results = $results->paginate($inputs['per_page'] ?? 20)->appends($inputs);

        return response()->fromPaginator($results, EjaculationResource::class);
    }
}
