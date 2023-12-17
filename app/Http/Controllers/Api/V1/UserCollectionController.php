<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserCollectionController extends Controller
{
    public function index(Request $request, User $user)
    {
        if (!$user->isMe() && $user->is_protected) {
            throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
        }

        $inputs = $request->validate([
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $query = $user->collections();
        if (!$user->isMe()) {
            $query = $query->where('is_private', false);
        }
        $collections = $query->orderBy('id')
            ->paginate($inputs['per_page'] ?? 20);

        return response()->fromPaginator($collections, CollectionResource::class);
    }
}
