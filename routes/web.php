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

Route::get('/user', function() {
    return redirect()->route('profile', ['name' => Auth::user()->name]);
})->middleware('auth')->name('profile');
Route::get('/user/{name?}', 'UserController@profile')->name('profile');

Route::middleware('auth')->group(function () {
    Route::get('/checkin', 'EjaculationController@create')->name('checkin');
    Route::post('/checkin', 'EjaculationController@store')->name('checkin');
});