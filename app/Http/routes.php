<?php

// Authenticated routes...
Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'books'], function () {

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
    Route::put('/images/update/{imageId}', 'ImageController@update');
    Route::delete('/images/{imageId}', 'ImageController@destroy');
    Route::get('/images/all/{page}', 'ImageController@getAll');
    Route::get('/images/{any}', 'ImageController@getImage')->where('any', '.*');

    // Links
    Route::get('/link/{id}', 'PageController@redirectFromLink');

    // Search
    Route::get('/search/all', 'SearchController@searchAll');
    Route::get('/search/book/{bookId}', 'SearchController@searchBook');

    // Other Pages
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');

    // Settings
    Route::get('/settings', 'SettingController@index');
    Route::post('/settings', 'SettingController@update');


});

// Login/Logout routes
Route::get('/login', 'Auth\AuthController@getLogin');
Route::post('/login', 'Auth\AuthController@postLogin');
Route::get('/logout', 'Auth\AuthController@getLogout');
// Password reset link request routes...
Route::get('/password/email', 'Auth\PasswordController@getEmail');
Route::post('/password/email', 'Auth\PasswordController@postEmail');
// Password reset routes...
Route::get('/password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('/password/reset', 'Auth\PasswordController@postReset');