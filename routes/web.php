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
Route::get('/checkin-tools', 'EjaculationController@tools')->name('checkin.tools');
Route::middleware('auth')->group(function () {
    Route::get('/checkin/{ejaculation}/report', 'EjaculationReportController@create')->name('checkin.report');
    Route::post('/checkin/{ejaculation}/report', 'EjaculationReportController@store')->name('checkin.report.store');

    Route::redirect('/setting', '/setting/profile', 301);
    Route::get('/setting/profile', 'SettingController@profile')->name('setting');
    Route::post('/setting/profile', 'SettingController@updateProfile')->name('setting.profile.update');
    Route::get('/setting/privacy', 'SettingController@privacy')->name('setting.privacy');
    Route::post('/setting/privacy', 'SettingController@updatePrivacy')->name('setting.privacy.update');
    Route::get('/setting/webhooks', 'SettingController@webhooks')->name('setting.webhooks');
    Route::post('/setting/webhooks', 'SettingController@storeWebhooks')->name('setting.webhooks.store');
    Route::delete('/setting/webhooks/{webhook}', 'SettingController@destroyWebhooks')->name('setting.webhooks.destroy');
    Route::get('/setting/tokens', 'Setting\TokenController@index')->name('setting.tokens');
    Route::post('/setting/tokens', 'Setting\TokenController@store')->name('setting.tokens.store');
    Route::delete('/setting/tokens/{id}', 'Setting\TokenController@revoke')->name('setting.tokens.revoke');
    Route::get('/setting/import', 'SettingController@import')->name('setting.import');
    Route::post('/setting/import', 'SettingController@storeImport')->name('setting.import');
    Route::delete('/setting/import', 'SettingController@destroyImport')->name('setting.import.destroy');
    Route::get('/setting/export', 'SettingController@export')->name('setting.export');
    Route::get('/setting/export/csv', 'SettingController@exportToCsv')->name('setting.export.csv');
    Route::get('/setting/deactivate', 'SettingController@deactivate')->name('setting.deactivate');
    Route::post('/setting/deactivate', 'SettingController@destroyUser')->name('setting.deactivate.destroy');
    Route::get('/setting/password', 'SettingController@password')->name('setting.password');
    Route::post('/setting/password', 'SettingController@updatePassword')->name('setting.password.update');
    Route::get('/setting/filter/tags', 'Setting\TagFilterController@index')->name('setting.filter.tags');
    Route::post('/setting/filter/tags', 'Setting\TagFilterController@store')->name('setting.filter.tags.store');
    Route::delete('/setting/filter/tags/{tag_filter}', 'Setting\TagFilterController@destroy')->name('setting.filter.tags.destroy');
});

Route::get('/info', 'InfoController@index')->name('info');
Route::get('/info/{id}', 'InfoController@show')->where('id', '[0-9]+')->name('info.show');

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
        Route::get('/rules', 'RuleController@index')->name('rule');
        Route::get('/rules/create', 'RuleController@create')->name('rule.create');
        Route::post('/rules', 'RuleController@store')->name('rule.store');
        Route::get('/rules/{rule}', 'RuleController@edit')->name('rule.edit');
        Route::put('/rules/{rule}', 'RuleController@update')->name('rule.update');
        Route::delete('/rules/{rule}', 'RuleController@destroy')->name('rule.destroy');
        Route::get('/reports', 'ReportController@index')->name('reports');
        Route::get('/reports/{report}', 'ReportController@show')->name('reports.show');
        Route::post('/reports/{report}/action', 'ReportController@action')->name('reports.action');
    });

Route::view('/{path?}', 'spa')->where('path', '.*');
