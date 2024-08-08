<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\UserStats;

use App\Http\Controllers\Controller;
use App\Queries\CountUsedTags;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MostlyUsedCheckinTags extends Controller
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

        $result = (new CountUsedTags(Auth::user(), $user))
            ->since($since)
            ->until($until)
            ->setIncludesMetadata($request->boolean('includes_metadata'))
            ->query();

        return response()->json($result);
    }
}
