<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::group(['prefix' => 'books'], function() {

    Route::get('/', 'BookController@index');
    Route::get('/create', 'BookController@create');
    Route::post('/', 'BookController@store');
    Route::get('/{slug}/edit', 'BookController@edit');
    Route::put('/{slug}', 'BookController@update');
    Route::delete('/{id}/destroy', 'BookController@destroy');
    Route::get('/{slug}', 'BookController@show');
});

Route::get('/', function () {
    return view('base');
});
