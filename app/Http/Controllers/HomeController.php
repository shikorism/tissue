<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Information;
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
        $informations = Information::query()
            ->select('id', 'category', 'pinned', 'title', 'created_at')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        $categories = Information::CATEGORIES;

        if (Auth::check()) {
            // チェックイン動向グラフ用のデータ取得
            $groupByDay = Ejaculation::select(DB::raw(
                <<<'SQL'
to_char(ejaculated_date, 'YYYY/MM/DD') AS "date",
count(*) AS "count"
SQL
            ))
                ->join('users', function ($join) {
                    $join->on('users.id', '=', 'ejaculations.user_id')
                        ->where('users.accept_analytics', true);
                })
                ->where('ejaculated_date', '>=', now()->subDays(30))
                ->groupBy(DB::raw("to_char(ejaculated_date, 'YYYY/MM/DD')"))
                ->orderBy(DB::raw("to_char(ejaculated_date, 'YYYY/MM/DD')"))
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item['date'] => $item['count']];
                });
            $globalEjaculationCounts = [];
            $day = Carbon::now()->subDays(29);
            for ($i = 0; $i < 30; $i++) {
                $globalEjaculationCounts[$day->format('Y/m/d') . ' の総チェックイン数'] = $groupByDay[$day->format('Y/m/d')] ?? 0;
                $day->addDay();
            }

            // お惣菜コーナー用のデータ取得
            $publicLinkedEjaculations = Ejaculation::join('users', 'users.id', '=', 'ejaculations.user_id')
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
                ->take(21)
                ->get();

            return view('home')->with(compact('informations', 'categories', 'globalEjaculationCounts', 'publicLinkedEjaculations'));
        } else {
            return view('guest')->with(compact('informations', 'categories'));
        }
    }
}
