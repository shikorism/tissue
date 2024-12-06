<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserStats;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MostlyUsedLinks extends Controller
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
            $since = CarbonImmutable::now()->startOfDay()->firstOfYear();
            $until = CarbonImmutable::now()->startOfDay()->firstOfYear()->addYear();
        }

        return response()->json($this->countMostFrequentlyUsedOkazu($user, $since, $until));
    }

    private function countMostFrequentlyUsedOkazu(User $user, CarbonInterface $dateSince = null, CarbonInterface $dateUntil = null)
    {
        $sql = <<<SQL
SELECT normalized_link as link, count(*) as count
FROM ejaculations e
WHERE user_id = ? AND is_private IN (?, ?) AND ejaculated_date >= ? AND ejaculated_date < ? AND normalized_link <> ''
GROUP BY normalized_link HAVING count(*) >= 2
ORDER BY count DESC, normalized_link
LIMIT 10
SQL;

        if ($dateSince === null) {
            $dateSince = CarbonImmutable::create(1);
        }
        if ($dateUntil === null) {
            $dateUntil = now()->addMonth()->startOfMonth();
        }

        return DB::select($sql, [
            $user->id, false, Auth::check() && $user->id === Auth::id(), $dateSince, $dateUntil
        ]);
    }
}
