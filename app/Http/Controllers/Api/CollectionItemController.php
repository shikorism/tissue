<?php

namespace App\Http\Controllers\Api;

use App\Collection;
use App\CollectionItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollectionItemController extends Controller
{
    public function destroy(Collection $collection, CollectionItem $item)
    {
        $this->authorize('destroy', $item);
        $item->delete();

        return response()->noContent();
    }
}
