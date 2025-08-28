<?php

namespace App\Http\Controllers\Api\V1;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserLikeController extends Controller
{
    public function index(Request $request, User $user)
    {
        if (!$user->isMe() && ($user->is_protected || $user->private_likes)) {
            throw new AccessDeniedHttpException('このユーザーのいいねは表示できません');
        }

        $inputs = $request->validate([
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $query = $user->likes()
            ->select('likes.*')
            ->orderBy('likes.id', 'desc')
            ->with([
                'ejaculation' => function ($query) {
                    $query->with('user', 'tags')->withMutedStatus();
                }
            ])
            ->join('ejaculations', 'likes.ejaculation_id', '=', 'ejaculations.id')
            ->leftJoinSub(Ejaculation::queryTagFilterMatches(), 'tag_filter_matches', 'ejaculations.id', '=', 'tag_filter_matches.ejaculation_id')
            ->where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query->where('ejaculations.user_id', Auth::id())
                            ->orWhere('ejaculations.is_private', false);
                    })->where(function ($query) {
                        $query->where('ejaculations.user_id', Auth::id())
                            ->orWhereRaw('COALESCE(tag_filter_matches.is_removed_by_tag_filter, 0) < 1');
                    });
            });

        $likes = $query->paginate($inputs['per_page'] ?? 20);
        $likes->getCollection()->transform(fn ($like) => $like->ejaculation);

        return response()->fromPaginator($likes, EjaculationResource::class);
    }
}
