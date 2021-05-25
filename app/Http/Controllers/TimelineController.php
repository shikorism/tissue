<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimelineController extends Controller
{
    public function showPublic()
    {
        $ejaculations = Ejaculation::join('users', 'users.id', '=', 'ejaculations.user_id')
            ->where('users.is_protected', false)
            ->where('ejaculations.is_private', false)
            ->where('ejaculations.link', '<>', '')
            ->where('ejaculations.ejaculated_date', '<=', Carbon::now())
            ->orderBy('ejaculations.ejaculated_date', 'desc')
            ->select('ejaculations.*')
            ->with('user', 'tags')
            ->withLikes()
            ->withMutedStatus()
            ->visibleToTimeline()
            ->paginate(21);

        return view('timeline.public')->with(compact('ejaculations'));
    }
}
