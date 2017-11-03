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
to_char(ejaculated_date, 'YYYY/MM/DD HH24:MI') AS ejaculated_date,
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

        // 現在のオナ禁セッションの経過時間
        if (count($ejaculations) > 0) {
            $currentSession = Carbon::parse($ejaculations[0]['ejaculated_date'])
                ->diff(Carbon::now())
                ->format('%a日 %h時間 %i分');
        } else {
            $currentSession = null;
        }

        // 概況欄のデータ取得
        $summary = DB::select(<<<'SQL'
SELECT
  avg(span) AS average,
  max(span) AS longest,
  min(span) AS shortest,
  sum(span) AS total_times,
  count(*) AS total_checkins
FROM
  (
    SELECT
      extract(epoch from ejaculated_date - lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)) AS span
    FROM
      ejaculations
    WHERE
      user_id = :user_id
    ORDER BY
      ejaculated_date DESC
  ) AS temp
SQL
            , ['user_id' => $user->id]);

        return view('user.profile')->with(compact('user', 'ejaculations', 'currentSession', 'summary'));
    }
}
