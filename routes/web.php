<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::get('/user', 'UserController@redirectMypage')->middleware('auth');
Route::get('/user/{name?}', 'UserController@profile')->name('user.profile');
Route::get('/user/{name}/stats', 'UserController@stats')->name('user.stats');
Route::get('/user/{name}/okazu', 'UserController@okazu')->name('user.okazu');
Route::get('/user/{name}/likes', 'UserController@likes')->name('user.likes');

Route::get('/checkin/{id}', 'EjaculationController@show')->name('checkin.show');
Route::middleware('auth')->group(function () {
    Route::get('/checkin', 'EjaculationController@create')->name('checkin');
    Route::post('/checkin', 'EjaculationController@store')->name('checkin');
    Route::get('/checkin/{id}/edit', 'EjaculationController@edit')->name('checkin.edit');
    Route::put('/checkin/{id}', 'EjaculationController@update')->name('checkin.update');
    Route::delete('/checkin/{id}', 'EjaculationController@destroy')->name('checkin.destroy');

    Route::get('/timeline/public', 'TimelineController@showPublic')->name('timeline.public');

    Route::redirect('/setting', '/setting/profile', 301);
    Route::get('/setting/profile', 'SettingController@profile')->name('setting');
    Route::post('/setting/profile', 'SettingController@updateProfile')->name('setting.profile.update');
    Route::get('/setting/privacy', 'SettingController@privacy')->name('setting.privacy');
    Route::post('/setting/privacy', 'SettingController@updatePrivacy')->name('setting.privacy.update');
//    Route::get('/setting/password', 'SettingController@password')->name('setting.password');
});

Route::get('/info', 'InfoController@index')->name('info');
Route::get('/info/{id}', 'InfoController@show')->where('id', '[0-9]+')->name('info.show');

Route::redirect('/search', '/search/checkin', 301);
Route::get('/search/checkin', 'SearchController@index')->name('search');
Route::get('/search/related-tag', 'SearchController@relatedTag')->name('search.related-tag');

Route::middleware('can:admin')
    ->namespace('Admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', 'DashboardController@index')->name('dashboard');
        Route::get('/info', 'InfoController@index')->name('info');
        Route::get('/info/create', 'InfoController@create')->name('info.create');
        Route::post('/info', 'InfoController@store')->name('info.store');
        Route::get('/info/{info}', 'InfoController@edit')->name('info.edit');
        Route::put('/info/{info}', 'InfoController@update')->name('info.update');
        Route::delete('/info/{info}', 'InfoController@destroy')->name('info.destroy');
    });
