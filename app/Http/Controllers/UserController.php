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
link,
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

        $groupByDay = Ejaculation::select(DB::raw(<<<'SQL'
to_char(ejaculated_date, 'YYYY/MM/DD') AS "date",
count(*) AS "count"
SQL
        ))
            ->where('user_id', $user->id)
            ->groupBy(DB::raw("to_char(ejaculated_date, 'YYYY/MM/DD')"))
            ->orderBy(DB::raw("to_char(ejaculated_date, 'YYYY/MM/DD')"))
            ->get();

        $dailySum = [];
        $monthlySum = [];
        $yearlySum = [];

        // 年間グラフ用の配列初期化
        if ($groupByDay->first() !== null) {
            $year = Carbon::createFromFormat('Y/m/d', $groupByDay->first()->date)->year;
            $currentYear = date('Y');
            for (; $year <= $currentYear; $year++) {
                $yearlySum[$year] = 0;
            }
        }

        // 月間グラフ用の配列初期化
        $month = Carbon::now()->subMonth(11)->firstOfMonth(); // 直近12ヶ月
        for ($i = 0; $i < 12; $i++) {
            $monthlySum[$month->format('Y/m')] = 0;
            $month->addMonth();
        }

        foreach ($groupByDay as $data) {
            $date = Carbon::createFromFormat('Y/m/d', $data->date);
            $yearAndMonth = $date->format('Y/m');

            $dailySum[$date->timestamp] = $data->count;
            $yearlySum[$date->year] += $data->count;
            if (isset($monthlySum[$yearAndMonth])) {
                $monthlySum[$yearAndMonth] += $data->count;
            }
        }

        return view('user.stats')->with(compact('user', 'dailySum', 'monthlySum', 'yearlySum'));
    }

    public function okazu($name)
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
link,
to_char(lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC), 'YYYY/MM/DD HH24:MI') AS before_date,
to_char(ejaculated_date - (lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)), 'FMDDD日 FMHH24時間 FMMI分') AS ejaculated_span
SQL
        ))
            ->where('user_id', $user->id)
            ->where('link', '<>', '');
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('is_private', false);
        }
        $ejaculations = $query->orderBy('ejaculated_date', 'desc')
            ->paginate(20);

        return view('user.profile')->with(compact('user', 'ejaculations'));
    }
}
