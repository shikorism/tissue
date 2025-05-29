<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function __invoke()
    {
        $tags = Tag::select(DB::raw(
            <<<'SQL'
tags.name,
count(*) AS "checkins_count"
SQL
        ))
            ->join('ejaculation_tag', 'tags.id', '=', 'ejaculation_tag.tag_id')
            ->join('ejaculations', 'ejaculations.id', '=', 'ejaculation_tag.ejaculation_id')
            ->join('users', 'users.id', '=', 'ejaculations.user_id')
            ->where('ejaculations.is_private', false)
            ->where(function ($query) {
                $query->where('users.is_protected', false);
                if (Auth::check()) {
                    $query->orWhere('users.id', Auth::id());
                }
            })
            ->groupBy('tags.name')
            ->orderByDesc('checkins_count')
            ->orderBy('tags.name')
            ->paginate(100);

        return response()->fromPaginator($tags, TagResource::class);
    }
}
