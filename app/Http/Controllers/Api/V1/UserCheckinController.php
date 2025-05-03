<?php

namespace App\Http\Controllers\Api\V1;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use App\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserCheckinController extends Controller
{
    public function index(Request $request, User $user)
    {
        if (!$user->isMe() && $user->is_protected) {
            throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
        }

        $inputs = $request->validate([
            'per_page' => 'nullable|integer|between:10,100',
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'since' => 'nullable|date_format:Y-m-d|after_or_equal:2000-01-01|before_or_equal:2099-12-31',
            'until' => 'nullable|date_format:Y-m-d|after_or_equal:2000-01-01|before_or_equal:2099-12-31',
        ]);

        if (!empty($inputs['since']) && !empty($inputs['until'])) {
            $since = CarbonImmutable::createFromFormat('Y-m-d', $inputs['since'])->startOfDay();
            $until = CarbonImmutable::createFromFormat('Y-m-d', $inputs['until'])->startOfDay()->addDay();
            if ($until->isBefore($since)) {
                [$since, $until] = [$until, $since];
            }
        } elseif (!empty($inputs['since'])) {
            $since = CarbonImmutable::createFromFormat('Y-m-d', $inputs['since'])->startOfDay();
            $until = null;
        } elseif (!empty($inputs['until'])) {
            $since = null;
            $until = CarbonImmutable::createFromFormat('Y-m-d', $inputs['until'])->startOfDay()->addDay();
        } else {
            $since = null;
            $until = null;
        }

        $query = Ejaculation::select(DB::raw(
            <<<'SQL'
ejaculations.id,
ejaculated_date,
note,
is_private,
is_too_sensitive,
link,
source,
discard_elapsed_time,
user_id
SQL
        ))
            ->withInterval()
            ->where('user_id', $user->id);
        if ($request->boolean('has_link')) {
            $query = $query->where('link', '<>', '');
        }
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('is_private', false);
        }
        if ($since !== null) {
            $query = $query->where('ejaculated_date', '>=', $since);
        }
        if ($until !== null) {
            $query = $query->where('ejaculated_date', '<', $until);
        }
        $order = ($inputs['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $ejaculations = $query->orderBy('ejaculated_date', $order)
            ->with('tags', 'user')
            ->withLikes()
            ->withMutedStatus()
            ->paginate($inputs['per_page'] ?? 20);

        return response()->fromPaginator($ejaculations, EjaculationResource::class);
    }
}
