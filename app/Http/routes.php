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



// Authentication routes...
Route::group(['middleware' => 'auth'], function() {

    Route::group(['prefix' => 'books'], function() {

        // Books
        Route::get('/', 'BookController@index');
        Route::get('/create', 'BookController@create');
        Route::post('/', 'BookController@store');
        Route::get('/{slug}/edit', 'BookController@edit');
        Route::put('/{slug}', 'BookController@update');
        Route::delete('/{id}', 'BookController@destroy');
        Route::get('/{slug}', 'BookController@show');
        Route::get('/{slug}/delete', 'BookController@showDelete');

        // Pages
        Route::get('/{bookSlug}/page/create', 'PageController@create');
        Route::post('/{bookSlug}/page', 'PageController@store');
        Route::get('/{bookSlug}/sort', 'PageController@sortPages');
        Route::put('/{bookSlug}/sort', 'PageController@savePageSort');
        Route::get('/{bookSlug}/page/{pageSlug}', 'PageController@show');
        Route::get('/{bookSlug}/page/{pageSlug}/edit', 'PageController@edit');
        Route::get('/{bookSlug}/page/{pageSlug}/delete', 'PageController@showDelete');
        Route::put('/{bookSlug}/page/{pageSlug}', 'PageController@update');
        Route::delete('/{bookSlug}/page/{pageSlug}', 'PageController@destroy');
        //Revisions
        Route::get('/{bookSlug}/page/{pageSlug}/revisions', 'PageController@showRevisions');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}', 'PageController@showRevision');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', 'PageController@restoreRevision');

        // Chapters
        Route::get('/{bookSlug}/chapter/{chapterSlug}/create-page', 'PageController@create');
        Route::get('/{bookSlug}/chapter/create', 'ChapterController@create');
        Route::post('/{bookSlug}/chapter/create', 'ChapterController@store');
        Route::get('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@show');
        Route::put('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@update');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/edit', 'ChapterController@edit');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/delete', 'ChapterController@showDelete');
        Route::delete('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@destroy');

    });

    Route::post('/upload/image', 'ImageController@upload');

    // Users
    Route::get('/users', 'UserController@index');
    Route::get('/users/create', 'UserController@create');
    Route::get('/users/{id}/delete', 'UserController@delete');
    Route::post('/users/create', 'UserController@store');
    Route::get('/users/{id}', 'UserController@edit');
    Route::put('/users/{id}', 'UserController@update');
    Route::delete('/users/{id}', 'UserController@destroy');

    // Image routes
    Route::get('/images/all', 'ImageController@getAll');
    Route::get('/images/all/{page}', 'ImageController@getAll');
    Route::get('/images/{any}', 'ImageController@getImage')->where('any', '.*');

    // Links
    Route::get('/link/{id}', 'PageController@redirectFromLink');

    // Search
    Route::get('/pages/search/all', 'PageController@searchAll');

    Route::get('/', function () {
        return view('home');
    });
    Route::get('/home', function () {
        return view('home');
    });


});

Route::get('/login', 'Auth\AuthController@getLogin');
Route::post('/login', 'Auth\AuthController@postLogin');
Route::get('/logout', 'Auth\AuthController@getLogout');
