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

// Private API
Route::middleware('stateful')->group(function () {
    Route::get('/checkin/card', 'Api\\CardController@show')
        ->middleware('throttle:30|180,1,card');

    Route::middleware('throttle:60,1')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('/me', 'Api\\V1\\MeController@show')->name('me.show');
            Route::post('/likes', 'Api\\LikeController@store');
            Route::delete('/likes/{id}', 'Api\\LikeController@destroy');
            Route::apiResource('checkin', 'Api\\CheckinController')->only(['destroy']);
            Route::post('/collections/inbox', 'Api\\CollectionController@inbox');
        });

        Route::apiResource('users.collections', 'Api\\CollectionController')->only(['index']);
        Route::apiResource('collections', 'Api\\CollectionController')->only(['show']);
        Route::apiResource('collections.items', 'Api\\CollectionItemController')->only(['index', 'update', 'destroy']);
    });
});

// Public Webhooks
Route::post('/webhooks/checkin/{webhook}', 'Api\\WebhookController@checkin')
    ->middleware('throttle:15,15,checkin_webhook');

// Public API
Route::middleware(['throttle:60,1', 'auth:api'])
    ->namespace('Api\\V1')
    ->prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::get('me', 'MeController@show')->name('me.show');
        Route::apiResource('users', 'UserController')->only(['show']);
        Route::apiResource('users.checkins', 'UserCheckinController')->only(['index']);
        Route::apiResource('checkins', 'CheckinController')->except(['index']);
    });
