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
            Route::apiResource('collections', 'Api\\V1\\CollectionController')->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('collections.items', 'Api\\V1\\CollectionItemController')->only(['store', 'update', 'destroy']);
            Route::get('/recent-tags', 'Api\\V1\\RecentTagsController')->name('recent-tags');
        });

        Route::apiResource('users.collections', 'Api\\UserCollectionController')->only(['index']);
        Route::apiResource('collections', 'Api\\V1\\CollectionController')->only(['show']);
        Route::apiResource('collections.items', 'Api\\V1\\CollectionItemController')->only(['index']);

        Route::namespace('Api\\V1\\UserStats')
            ->prefix('users/{user}/stats')
            ->name('users.stats.')
            ->group(function () {
                Route::get('/checkin/daily', 'DailyCheckinSummary')->name('checkin.daily');
                Route::get('/checkin/hourly', 'HourlyCheckinSummary')->name('checkin.hourly');
                Route::get('/links', 'MostlyUsedLinks')->name('links');
                Route::get('/tags', 'MostlyUsedCheckinTags')->name('tags');
            });
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
        Route::apiResource('users.collections', 'UserCollectionController')->only(['index']);
        Route::apiResource('collections', 'CollectionController')->except(['index']);
        Route::apiResource('collections.items', 'CollectionItemController')->except(['show']);

        Route::namespace('UserStats')
            ->prefix('users/{user}/stats')
            ->name('users.stats.')
            ->group(function () {
                Route::get('/checkin/daily', 'DailyCheckinSummary')->name('checkin.daily');
                Route::get('/checkin/hourly', 'HourlyCheckinSummary')->name('checkin.hourly');
                Route::get('/links', 'MostlyUsedLinks')->name('links');
                Route::get('/tags', 'MostlyUsedCheckinTags')->name('tags');
            });
    });
