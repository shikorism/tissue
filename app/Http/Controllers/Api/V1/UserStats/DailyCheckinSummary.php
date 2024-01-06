<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserStats;

use App\Http\Controllers\Controller;
use App\Queries\EjaculationCountByDay;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DailyCheckinSummary extends Controller
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

        $countByDay = (new EjaculationCountByDay($user))->query();
        if ($since !== null) {
            $countByDay = $countByDay->where('ejaculated_date', '>=', $since);
        }
        if ($until !== null) {
            $countByDay = $countByDay->where('ejaculated_date', '<', $until);
        }

        return response()->json($countByDay->get());
    }
}
