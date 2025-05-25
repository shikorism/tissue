<?php

namespace App\Http\Controllers\Api\UserStats;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OldestCheckinDate extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        if (!$user->isMe() && $user->is_protected) {
            throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
        }

        $oldest = $user->ejaculations()->orderBy('ejaculated_date')->first();

        return response()->json(['oldest_checkin_date' => $oldest?->ejaculated_date?->format('Y-m-d')]);
    }
}
