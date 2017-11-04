<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //

    public function profile($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        // チェックインの取得
        $query = Ejaculation::select(DB::raw(<<<'SQL'
id,
ejaculated_date,
note,
is_private,
to_char(lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC), 'YYYY/MM/DD HH24:MI') AS before_date,
to_char(ejaculated_date - (lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)), 'FMDDD日 FMHH24時間 FMMI分') AS ejaculated_span
SQL
        ))
            ->where('user_id', $user->id);
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('is_private', false);
        }
        $ejaculations = $query->orderBy('ejaculated_date', 'desc')
            ->paginate(20);

        return view('user.profile')->with(compact('user', 'ejaculations'));
    }

    public function stats($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        return view('user.stats')->with(compact('user'));
    }

    public function okazu($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        return view('user.okazu')->with(compact('user'));
    }
}
