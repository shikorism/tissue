<?php

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/checkin/card', 'Api\\CardController@show')
    ->middleware('throttle:180,1,card');

Route::middleware(['throttle:60,1', 'stateful', 'auth'])->group(function () {
    Route::post('/likes', 'Api\\LikeController@store');
    Route::delete('/likes/{id}', 'Api\\LikeController@destroy');
});

Route::post('/webhooks/checkin/{webhook}', 'Api\\WebhookController@checkin')
    ->middleware('throttle:15,15,checkin_webhook');
