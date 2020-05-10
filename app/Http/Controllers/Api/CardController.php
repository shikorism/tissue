<?php

namespace App\Http\Controllers\Api;

use App\Services\MetadataResolveService;
use Illuminate\Http\Request;

class CardController
{
    public function show(Request $request, MetadataResolveService $service)
    {
        $request->validate([
            'url:required|url'
        ]);

        $metadata = $service->execute($request->input('url'));
        $metadata->load('tags');

        $response = response($metadata);
        if (!config('app.debug')) {
            $response = $response->setCache(['public' => true, 'max_age' => 86400]);
        }

        return $response;
    }
}
