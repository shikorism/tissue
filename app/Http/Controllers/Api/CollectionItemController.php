<?php

namespace App\Http\Controllers\Api;

use App\Collection;
use App\CollectionItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionItemResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CollectionItemController extends Controller
{
    public function index(Request $request, Collection $collection)
    {
        if (!$collection->user->isMe()) {
            if ($collection->is_private) {
                throw new NotFoundHttpException();
            }
            if ($collection->user->is_protected) {
                throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
            }
        }

        $inputs = $request->validate([
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $items = $collection->items()
            ->orderByDesc('id')
            ->paginate($inputs['per_page'] ?? 20);

        return response()->fromPaginator($items, CollectionItemResource::class);
    }

    public function destroy(Collection $collection, CollectionItem $item)
    {
        $this->authorize('destroy', $item);
        $item->delete();

        return response()->noContent();
    }
}
