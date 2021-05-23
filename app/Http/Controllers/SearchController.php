<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Parser\SearchQuery\InvalidExpressionException;
use App\Parser\SearchQueryParser;
use App\Tag;
use App\Utilities\Formatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $q = $this->normalizeQuery($inputs['q']);
        $results = Ejaculation::query()
            ->whereHas('user', function ($query) {
                $query->where('is_protected', false);
                if (Auth::check()) {
                    $query->orWhere('id', Auth::id());
                }
            })
            ->where('is_private', false)
            ->orderBy('ejaculated_date', 'desc')
            ->with(['user', 'tags'])
            ->withLikes()
            ->withMutedStatus();

        try {
            $parser = (new SearchQueryParser())->parse($q);
            foreach ($parser->getExpressions() as $expression) {
                switch ($expression->target) {
                    case 'date': {
                        $op = $expression->negative ? '<>' : '=';
                        $results = $results->whereDate('ejaculated_date', $op, $expression->keyword);
                        break;
                    }
                    case 'since':
                        $results = $results->whereDate('ejaculated_date', '>=', $expression->keyword);
                        break;
                    case 'until':
                        $results = $results->whereDate('ejaculated_date', '<=', $expression->keyword);
                        break;
                    case 'link':
                    case 'url': {
                        $op = $expression->negative ? 'not like' : 'like';
                        $results = $results->where('link', $op, "%{$expression->keyword}%");
                        break;
                    }
                    case 'note': {
                        $op = $expression->negative ? 'not like' : 'like';
                        $results = $results->where('note', $op, "%{$expression->keyword}%");
                        break;
                    }
                    case 'tag':
                        $results = $results->whereHas('tags', function ($query) use ($expression) {
                            $op = $expression->negative ? 'not like' : 'like';
                            $query->where('normalized_name', $op, "%{$expression->keyword}%");
                        });
                        break;
                    case 'user':
                        $results = $results->whereHas('user', function ($query) use ($expression) {
                            $op = $expression->negative ? '<>' : '=';
                            $query->where('name', $op, $expression->keyword);
                        });
                        break;
                    case 'is':
                        switch ($expression->keyword) {
                            case 'sensitive':
                                $results = $results->where('is_too_sensitive', !$expression->negative);
                                break;
                        }
                        break;
                }
            }
        } catch (InvalidExpressionException $e) {
            $results = collect();
        }

        $results = $results->paginate(20)->appends($inputs);

        return view('search.index')->with(compact('inputs', 'results'));
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
