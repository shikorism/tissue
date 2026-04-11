<?php

namespace App\Http\Controllers\Api\V1\Stats;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyCheckinSummary extends Controller
{
    public function __invoke()
    {
        $groupByDay = Ejaculation::select(DB::raw(
            <<<'SQL'
to_char(ejaculated_date, 'YYYY-MM-DD') AS "date",
count(*) AS "count"
SQL
        ))
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'ejaculations.user_id')
                    ->where('users.accept_analytics', true);
            })
            ->where('ejaculated_date', '>=', now()->subDays(30))
            ->groupBy(DB::raw("to_char(ejaculated_date, 'YYYY-MM-DD')"))
            ->orderBy(DB::raw("to_char(ejaculated_date, 'YYYY-MM-DD')"))
            ->get()
            ->mapWithKeys(fn ($item) => [$item['date'] => $item['count']]);

        // 間欠部分の0埋め
        $globalEjaculationCounts = [];
        $day = Carbon::now()->subDays(29);
        for ($i = 0; $i < 30; $i++) {
            $globalEjaculationCounts[] = [
                'date' => $day->format('Y-m-d'),
                'count' => $groupByDay[$day->format('Y-m-d')] ?? 0,
            ];
            $day->addDay();
        }

        return response()->json($globalEjaculationCounts);
    }
}
