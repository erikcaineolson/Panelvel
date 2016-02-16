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
    return redirect(url('/home'));
});

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

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');

    // Domain Routes
    Route::get('/domains', [
        'as'   => 'domain.index',
        'uses' => 'DomainController@index',
    ]);

    Route::get('/domain/new', [
        'as'   => 'domain.create',
        'uses' => 'DomainController@create',
    ]);

    Route::post('/domain', [
        'as'   => 'domain.store',
        'uses' => 'DomainController@store',
    ]);

    Route::delete('/domain/{domain}', [
        'as'   => 'domain.destroy',
        'uses' => 'DomainController@destroy',
    ]);
});
