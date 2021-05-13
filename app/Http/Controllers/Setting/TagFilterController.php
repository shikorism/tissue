<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\TagFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TagFilterController extends Controller
{
    public function index()
    {
        $tagFilters = Auth::user()->tagFilters()->orderBy('tag_name')->get();
        $perUserLimit = TagFilter::PER_USER_LIMIT;

        return view('setting.filter.tags')->with(compact('tagFilters', 'perUserLimit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tag_filters', 'tag_name')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'mode' => [
                'required',
                'integer',
                Rule::in([TagFilter::MODE_MASK, TagFilter::MODE_REMOVE])
            ]
        ], [], [
            'tag_name' => 'タグ名',
            'mode' => '隠し方'
        ]);

        if (Auth::user()->tagFilters()->count() >= TagFilter::PER_USER_LIMIT) {
            return redirect()->route('setting.filter.tags')
                ->with('status', TagFilter::PER_USER_LIMIT . '件以上、タグミュートを設定することはできません。');
        }

        Auth::user()->tagFilters()->create($validated);

        return redirect()->route('setting.filter.tags')->with('status', '作成しました。');
    }

    public function destroy(TagFilter $tagFilter)
    {
        $tagFilter->delete();

        return redirect()->route('setting.filter.tags')->with('status', '削除しました。');
    }
}
