<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserStats;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HourlyCheckinSummary extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        if (!$user->isMe() && $user->is_protected) {
            throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
        }

        $validated = $request->validate([
            'since' => 'nullable|date_format:Y-m-d|after_or_equal:2000-01-01|before_or_equal:2099-12-31',
            'until' => 'nullable|date_format:Y-m-d|after_or_equal:2000-01-01|before_or_equal:2099-12-31',
        ]);

        if (!empty($validated['since']) && !empty($validated['until'])) {
            $since = CarbonImmutable::createFromFormat('Y-m-d', $validated['since'])->startOfDay();
            $until = CarbonImmutable::createFromFormat('Y-m-d', $validated['until'])->startOfDay()->addDay();
            if ($until->isBefore($since)) {
                [$since, $until] = [$until, $since];
            }
        } elseif (!empty($validated['since'])) {
            $since = CarbonImmutable::createFromFormat('Y-m-d', $validated['since'])->startOfDay();
            $until = null;
        } elseif (!empty($validated['until'])) {
            $since = null;
            $until = CarbonImmutable::createFromFormat('Y-m-d', $validated['until'])->startOfDay()->addDay();
        } else {
            $since = null;
            $until = null;
        }

        $dateCondition = [];
        if ($since !== null) {
            $dateCondition[] = ['ejaculated_date', '>=', $since];
        }
        if ($until !== null) {
            $dateCondition[] = ['ejaculated_date', '<', $until];
        }

        $groupByHour = Ejaculation::select(DB::raw(
            <<<'SQL'
to_char(ejaculated_date, 'HH24') AS "hour",
count(*) AS "count"
SQL
        ))
            ->where('user_id', $user->id)
            ->where($dateCondition)
            ->groupBy(DB::raw("to_char(ejaculated_date, 'HH24')"))
            ->orderBy(DB::raw('1'))
            ->get();

        $results = [];
        for ($hour = 0; $hour < 24; $hour++) {
            if (!empty($groupByHour) && (int)($groupByHour->first()->hour) === $hour) {
                $data = $groupByHour->shift();
                $results[] = ['hour' => $hour, 'count' => $data->count];
            } else {
                $results[] = ['hour' => $hour, 'count' => 0];
            }
        }

        return response()->json($results);
    }
}
