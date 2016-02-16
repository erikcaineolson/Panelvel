<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('login');
});

// Authentication Routes
Route::auth();

// Domain Routes
Route::get('/admin/domains', [
    'as' => 'domain.index',
    'DomainController@index',
]);
Route::get('/admin/domain/new', [
    'as'   => 'domain.create',
    'uses' => 'DomainController@create',
]);
Route::post('/admin/domain', [
    'as'   => 'domain.store',
    'uses' => 'DomainController@store',
]);
Route::delete('/admin/domain/{domain}', [
    'as'   => 'domain.destroy',
    'uses' => 'DomainController@destroy',
]);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
});
