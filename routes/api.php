<?php

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api.
 */
Route::get('docs.json', 'ApiDocsController@json');

Route::get('attachments', 'AttachmentApiController@list');
Route::post('attachments', 'AttachmentApiController@create');
Route::get('attachments/{id}', 'AttachmentApiController@read');
Route::put('attachments/{id}', 'AttachmentApiController@update');
Route::delete('attachments/{id}', 'AttachmentApiController@delete');

Route::get('books', 'BookApiController@list');
Route::post('books', 'BookApiController@create');
Route::get('books/{id}', 'BookApiController@read');
Route::put('books/{id}', 'BookApiController@update');
Route::delete('books/{id}', 'BookApiController@delete');

Route::get('books/{id}/export/html', 'BookExportApiController@exportHtml');
Route::get('books/{id}/export/pdf', 'BookExportApiController@exportPdf');
Route::get('books/{id}/export/plaintext', 'BookExportApiController@exportPlainText');
Route::get('books/{id}/export/markdown', 'BookExportApiController@exportMarkdown');

Route::get('chapters', 'ChapterApiController@list');
Route::post('chapters', 'ChapterApiController@create');
Route::get('chapters/{id}', 'ChapterApiController@read');
Route::put('chapters/{id}', 'ChapterApiController@update');
Route::delete('chapters/{id}', 'ChapterApiController@delete');

Route::get('chapters/{id}/export/html', 'ChapterExportApiController@exportHtml');
Route::get('chapters/{id}/export/pdf', 'ChapterExportApiController@exportPdf');
Route::get('chapters/{id}/export/plaintext', 'ChapterExportApiController@exportPlainText');
Route::get('chapters/{id}/export/markdown', 'ChapterExportApiController@exportMarkdown');

Route::get('pages', 'PageApiController@list');
Route::post('pages', 'PageApiController@create');
Route::get('pages/{id}', 'PageApiController@read');
Route::put('pages/{id}', 'PageApiController@update');
Route::delete('pages/{id}', 'PageApiController@delete');

Route::get('pages/{id}/export/html', 'PageExportApiController@exportHtml');
Route::get('pages/{id}/export/pdf', 'PageExportApiController@exportPdf');
Route::get('pages/{id}/export/plaintext', 'PageExportApiController@exportPlainText');
Route::get('pages/{id}/export/markdown', 'PageExportApiController@exportMarkDown');

Route::get('shelves', 'BookshelfApiController@list');
Route::post('shelves', 'BookshelfApiController@create');
Route::get('shelves/{id}', 'BookshelfApiController@read');
Route::put('shelves/{id}', 'BookshelfApiController@update');
Route::delete('shelves/{id}', 'BookshelfApiController@delete');
