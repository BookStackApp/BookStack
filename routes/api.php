<?php

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api
 */

Route::get('books', 'BooksApiController@index');
Route::post('books', 'BooksApiController@create');
Route::get('books/{id}', 'BooksApiController@read');
Route::put('books/{id}', 'BooksApiController@update');
Route::delete('books/{id}', 'BooksApiController@delete');
