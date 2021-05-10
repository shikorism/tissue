<?php

namespace App\Http\Controllers\Api;

use App\MetadataResolver\DeniedHostException;
use App\Services\MetadataResolveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardController
{
    public function show(Request $request, MetadataResolveService $service)
    {
        $request->validate([
            'url:required|url'
        ]);

        try {
            $metadata = $service->execute($request->input('url'));
        } catch (DeniedHostException $e) {
            abort(403, $e->getMessage());
        }
        $metadata->load('tags');

        $response = response($metadata);
        if (!config('app.debug')) {
            $response = $response->setCache(['public' => true, 'max_age' => 86400]);
        }

        if (env('METADATA_NO_CACHE', false)) {
            // MetadataResolverServiceで保存させずに上手くやるのが難しかったので適当にやってる、ゆるして
            DB::transaction(function () use ($metadata) {
                $metadata->tags()->detach();
                $metadata->delete();
            });
        }

        return $response;
    }
}
