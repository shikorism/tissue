<?php

namespace App\Http\Controllers\Api\V1;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        ]);

        $query = Ejaculation::select(DB::raw(
            <<<'SQL'
ejaculations.id,
ejaculated_date,
note,
is_private,
is_too_sensitive,
link,
source,
discard_elapsed_time
SQL
        ))
            ->where('user_id', $user->id);
        if ($request->boolean('has_link')) {
            $query = $query->where('link', '<>', '');
        }
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('is_private', false);
        }
        $ejaculations = $query->orderBy('ejaculated_date', 'desc')
            ->with('tags')
            ->withLikes()
            ->withMutedStatus()
            ->paginate($inputs['per_page'] ?? 20);

        return response()->fromPaginator($ejaculations, EjaculationResource::class);
    }
}
