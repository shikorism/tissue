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

Route::get('/user', function () {
    return redirect()->route('user.profile', ['name' => Auth::user()->name]);
})->middleware('auth')->name('user.profile');
Route::get('/user/{name?}', 'UserController@profile')->name('user.profile');
Route::get('/user/{name}/stats', 'UserController@stats')->name('user.stats');
Route::get('/user/{name}/okazu', 'UserController@okazu')->name('user.okazu');

Route::get('/checkin/{id}', 'EjaculationController@show')->name('checkin.show');
Route::middleware('auth')->group(function () {
    Route::get('/checkin', 'EjaculationController@create')->name('checkin');
    Route::post('/checkin', 'EjaculationController@store')->name('checkin');
    Route::get('/checkin/{id}/edit', 'EjaculationController@edit')->name('checkin.edit');
    Route::put('/checkin/{id}', 'EjaculationController@update')->name('checkin.update');
    Route::delete('/checkin/{id}', 'EjaculationController@destroy')->name('checkin.destroy');
});

Route::get('/info', 'InfoController@index')->name('info');
Route::get('/info/{id}', 'InfoController@show')->where('id', '[0-9]+')->name('info.show');

Route::redirect('/search', '/search/checkin', 301);
Route::get('/search/checkin', 'SearchController@index')->name('search');
Route::get('/search/related-tag', 'SearchController@relatedTag')->name('search.related-tag');

Route::redirect('/setting', '/setting/profile', 301);
Route::get('/setting/profile', 'SettingController@profile')->name('setting');
Route::get('/setting/password', 'SettingController@password')->name('setting.password');
