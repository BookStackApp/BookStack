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

    Route::get('/{bookSlug}/page/create', 'PageController@create');
    Route::post('/{bookSlug}/page', 'PageController@store');
    Route::get('/{bookSlug}/sort', 'PageController@sortPages');
    Route::put('/{bookSlug}/sort', 'PageController@savePageSort');
    Route::get('/{bookSlug}/page/{pageSlug}', 'PageController@show');
    Route::get('/{bookSlug}/page/{pageSlug}/create', 'PageController@create');
    Route::get('/{bookSlug}/page/{pageSlug}/edit', 'PageController@edit');
    Route::put('/{bookSlug}/page/{pageSlug}', 'PageController@update');

    Route::get('/{bookSlug}/chapter/create', 'ChapterController@create');
    Route::post('/{bookSlug}/chapter/create', 'ChapterController@store');

});

Route::post('/upload/image', 'ImageController@upload');

Route::get('/images/all', 'ImageController@getAll');
Route::get('/images/all/{page}', 'ImageController@getAll');
Route::get('/images/{any}', 'ImageController@getImage')->where('any', '.*');

Route::get('/link/{id}', 'PageController@redirectFromLink');
Route::get('/pages/search/all', 'PageController@searchAll');

Route::get('/', function () {
    return view('base');
});
