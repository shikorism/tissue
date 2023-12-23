<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserStats;

use App\Http\Controllers\Controller;
use App\Queries\EjaculationCountByDay;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Calendar extends Controller
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

            if ($until->diffInYears($since) >= 1) {
                $until = $since->addYear();
            }
        } elseif (!empty($validated['since'])) {
            $since = CarbonImmutable::createFromFormat('Y-m-d', $validated['since'])->startOfDay();
            $until = $since->addYear();
        } elseif (!empty($validated['until'])) {
            $until = CarbonImmutable::createFromFormat('Y-m-d', $validated['until'])->startOfDay()->addDay();
            $since = $until->subYear();
        } else {
            $until = CarbonImmutable::tomorrow()->startOfDay();
            $since = $until->subYear();
        }

        $countByDay = (new EjaculationCountByDay($user))->query()
            ->where('ejaculated_date', '>=', $since)
            ->where('ejaculated_date', '<', $until)
            ->get();

        $countByTimestamp = [];
        foreach ($countByDay as $data) {
            $date = CarbonImmutable::createFromFormat('Y/m/d', $data->date)->startOfDay();
            $countByTimestamp[$date->timestamp] = $data->count;
        }

        return response()->json([
            'count_by_day' => $countByTimestamp,
        ], options: JSON_FORCE_OBJECT);
    }
}
