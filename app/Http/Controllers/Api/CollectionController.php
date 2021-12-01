<?php

namespace App\Http\Controllers\Api;

use App\Collection;
use App\CollectionItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    public function inbox(Request $request)
    {
        /** @var Collection $collection */
        $collection = Auth::user()->collections()->firstOrCreate(['title' => 'あとで抜く']);

        $validated = $request->validate([
            'link' => [
                'required',
                'url',
                'max:2000',
                Rule::unique('collection_items', 'link')->where(function ($query) use ($collection) {
                    return $query->where('collection_id', $collection->id);
                })
            ]
        ]);

        $item = new CollectionItem($validated);
        $collection->items()->save($item);

        return response()->noContent();
    }
}
