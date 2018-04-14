<?php

use App\MetadataResolver\MetadataResolver;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/checkin/card', function (Request $request, MetadataResolver $resolver) {
    $request->validate([
        'url:required|url'
    ]);
    $url = $request->input('url');

    $metadata = $resolver->resolve($url);
    $response = response()->json($metadata);
    if (!config('app.debug')) {
        $response = $response->setCache(['public' => true, 'max_age' => 86400]);
    }
    return $response;
});