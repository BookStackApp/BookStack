<?php

use BookStack\Http\Controllers\Api\ApiDocsController;
use BookStack\Http\Controllers\Api\AttachmentApiController;
use BookStack\Http\Controllers\Api\BookApiController;
use BookStack\Http\Controllers\Api\BookExportApiController;
use BookStack\Http\Controllers\Api\BookshelfApiController;
use BookStack\Http\Controllers\Api\ChapterApiController;
use BookStack\Http\Controllers\Api\ChapterExportApiController;
use BookStack\Http\Controllers\Api\PageApiController;
use BookStack\Http\Controllers\Api\PageExportApiController;
use BookStack\Http\Controllers\Api\RecycleBinApiController;
use BookStack\Http\Controllers\Api\SearchApiController;
use BookStack\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api.
 */
Route::get('docs.json', [ApiDocsController::class, 'json']);

Route::get('attachments', [AttachmentApiController::class, 'list']);
Route::post('attachments', [AttachmentApiController::class, 'create']);
Route::get('attachments/{id}', [AttachmentApiController::class, 'read']);
Route::put('attachments/{id}', [AttachmentApiController::class, 'update']);
Route::delete('attachments/{id}', [AttachmentApiController::class, 'delete']);

Route::get('books', [BookApiController::class, 'list']);
Route::post('books', [BookApiController::class, 'create']);
Route::get('books/{id}', [BookApiController::class, 'read']);
Route::put('books/{id}', [BookApiController::class, 'update']);
Route::delete('books/{id}', [BookApiController::class, 'delete']);

Route::get('books/{id}/export/html', [BookExportApiController::class, 'exportHtml']);
Route::get('books/{id}/export/pdf', [BookExportApiController::class, 'exportPdf']);
Route::get('books/{id}/export/plaintext', [BookExportApiController::class, 'exportPlainText']);
Route::get('books/{id}/export/markdown', [BookExportApiController::class, 'exportMarkdown']);

Route::get('chapters', [ChapterApiController::class, 'list']);
Route::post('chapters', [ChapterApiController::class, 'create']);
Route::get('chapters/{id}', [ChapterApiController::class, 'read']);
Route::put('chapters/{id}', [ChapterApiController::class, 'update']);
Route::delete('chapters/{id}', [ChapterApiController::class, 'delete']);

Route::get('chapters/{id}/export/html', [ChapterExportApiController::class, 'exportHtml']);
Route::get('chapters/{id}/export/pdf', [ChapterExportApiController::class, 'exportPdf']);
Route::get('chapters/{id}/export/plaintext', [ChapterExportApiController::class, 'exportPlainText']);
Route::get('chapters/{id}/export/markdown', [ChapterExportApiController::class, 'exportMarkdown']);

Route::get('pages', [PageApiController::class, 'list']);
Route::post('pages', [PageApiController::class, 'create']);
Route::get('pages/{id}', [PageApiController::class, 'read']);
Route::put('pages/{id}', [PageApiController::class, 'update']);
Route::delete('pages/{id}', [PageApiController::class, 'delete']);

Route::get('pages/{id}/export/html', [PageExportApiController::class, 'exportHtml']);
Route::get('pages/{id}/export/pdf', [PageExportApiController::class, 'exportPdf']);
Route::get('pages/{id}/export/plaintext', [PageExportApiController::class, 'exportPlainText']);
Route::get('pages/{id}/export/markdown', [PageExportApiController::class, 'exportMarkDown']);

Route::get('search', [SearchApiController::class, 'all']);

Route::get('shelves', [BookshelfApiController::class, 'list']);
Route::post('shelves', [BookshelfApiController::class, 'create']);
Route::get('shelves/{id}', [BookshelfApiController::class, 'read']);
Route::put('shelves/{id}', [BookshelfApiController::class, 'update']);
Route::delete('shelves/{id}', [BookshelfApiController::class, 'delete']);

Route::get('users', [UserApiController::class, 'list']);
Route::post('users', [UserApiController::class, 'create']);
Route::get('users/{id}', [UserApiController::class, 'read']);
Route::put('users/{id}', [UserApiController::class, 'update']);
Route::delete('users/{id}', [UserApiController::class, 'delete']);

Route::get('recycle-bin', [RecycleBinApiController::class, 'list']);
Route::put('recycle-bin/{deletionId}', [RecycleBinApiController::class, 'restore']);
Route::delete('recycle-bin/{deletionId}', [RecycleBinApiController::class, 'destroy']);
