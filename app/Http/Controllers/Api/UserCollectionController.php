<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserCollectionController extends Controller
{
    public function index(User $user)
    {
        if (!$user->isMe() && $user->is_protected) {
            throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
        }

        $collections = $user->collections()->orderBy('id');
        if (!$user->isMe()) {
            $collections = $collections->where('is_private', false);
        }

        return response()->json($collections->get()->map(fn ($collection) => new CollectionResource($collection)));
    }
}
