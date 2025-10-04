<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Resources\TagResource;
use App\Tag;
use Illuminate\Http\Request;

class Tags extends BaseController
{
    public function __invoke(Request $request)
    {
        $inputs = $request->validate([
            'q' => 'required',
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $q = $this->normalizeQuery($inputs['q']);
        $results = Tag::query()
            ->where('normalized_name', 'like', "%{$q}%")
            ->paginate($inputs['per_page'] ?? 50)
            ->appends($inputs);

        return response()->fromPaginator($results, TagResource::class);
    }
}
