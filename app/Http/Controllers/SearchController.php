<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required'
        ]);

        $results = Ejaculation::query()
            ->whereHas('tags', function ($query) use ($inputs) {
                $query->where('name', 'like', "%{$inputs['q']}%");
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
            ->paginate(20)
            ->appends($inputs);

        return view('search.index')->with(compact('inputs', 'results'));
    }

    public function relatedTag(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required'
        ]);

        $results = Tag::query()
            ->where('name', 'like', "%{$inputs['q']}%")
            ->paginate(50)
            ->appends($inputs);

        return view('search.relatedTag')->with(compact('inputs', 'results'));
    }
}
