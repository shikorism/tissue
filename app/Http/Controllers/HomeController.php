<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $ejaculations = Ejaculation::select(DB::raw(<<<'SQL'
to_char(ejaculated_date, 'YYYY/MM/DD HH24:MI') AS ejaculated_date,
note,
is_private,
to_char(lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC), 'YYYY/MM/DD HH24:MI') AS before_date,
to_char(ejaculated_date - (lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)), 'FMDDD日 FMHH24時間 FMMI分') AS ejaculated_span
SQL
))
                ->where(['user_id' => Auth::id()])
                ->orderBy('ejaculated_date', 'desc')
                ->limit(9)
                ->get();

            // 現在のオナ禁セッションの経過時間
            if (count($ejaculations) > 0) {
                $currentSession = Carbon::parse($ejaculations[0]['ejaculated_date'])
                    ->diff(Carbon::now())
                    ->format('%d日 %h時間 %i分');
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
, ['user_id' => Auth::id()]);

            return view('home')->with(compact('ejaculations', 'currentSession', 'summary'));
        } else {
            return view('guest');
        }
    }
}
