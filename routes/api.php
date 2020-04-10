<?php

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api
 */

Route::get('docs', 'ApiDocsController@display');
Route::get('docs.json', 'ApiDocsController@json');

Route::get('books', 'BooksApiController@list');
Route::post('books', 'BooksApiController@create');
Route::get('books/{id}', 'BooksApiController@read');
Route::put('books/{id}', 'BooksApiController@update');
Route::delete('books/{id}', 'BooksApiController@delete');

Route::get('books/{id}/export/html', 'BooksExportApiController@exportHtml');
Route::get('books/{id}/export/pdf', 'BooksExportApiController@exportPdf');
Route::get('books/{id}/export/plaintext', 'BooksExportApiController@exportPlainText');

Route::get('shelves', 'BookshelfApiController@list');
Route::post('shelves', 'BookshelfApiController@create');
Route::get('shelves/{id}', 'BookshelfApiController@read');
Route::put('shelves/{id}', 'BookshelfApiController@update');
Route::delete('shelves/{id}', 'BookshelfApiController@delete');