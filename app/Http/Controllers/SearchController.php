<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Tag;
use App\Utilities\Formatter;
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
            ->whereHas('tags', function ($query) use ($q) {
                $query->where('normalized_name', 'like', "%{$q}%");
            })
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
            ->paginate(20)
            ->appends($inputs);

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
