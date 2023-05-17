<?php

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api.
 */

use BookStack\Api\ApiDocsController;
use BookStack\Entities\Controllers\BookApiController;
use BookStack\Entities\Controllers\BookExportApiController;
use BookStack\Entities\Controllers\BookshelfApiController;
use BookStack\Entities\Controllers\ChapterApiController;
use BookStack\Entities\Controllers\ChapterExportApiController;
use BookStack\Entities\Controllers\PageApiController;
use BookStack\Entities\Controllers\PageExportApiController;
use BookStack\Entities\Controllers\RecycleBinApiController;
use BookStack\Permissions\ContentPermissionApiController;
use BookStack\Search\SearchApiController;
use BookStack\Uploads\Controllers\AttachmentApiController;
use BookStack\Uploads\Controllers\ImageGalleryApiController;
use BookStack\Users\Controllers\RoleApiController;
use BookStack\Users\Controllers\UserApiController;
use Illuminate\Support\Facades\Route;

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
Route::get('pages/{id}/export/markdown', [PageExportApiController::class, 'exportMarkdown']);

Route::get('image-gallery', [ImageGalleryApiController::class, 'list']);
Route::post('image-gallery', [ImageGalleryApiController::class, 'create']);
Route::get('image-gallery/{id}', [ImageGalleryApiController::class, 'read']);
Route::put('image-gallery/{id}', [ImageGalleryApiController::class, 'update']);
Route::delete('image-gallery/{id}', [ImageGalleryApiController::class, 'delete']);

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

Route::get('roles', [RoleApiController::class, 'list']);
Route::post('roles', [RoleApiController::class, 'create']);
Route::get('roles/{id}', [RoleApiController::class, 'read']);
Route::put('roles/{id}', [RoleApiController::class, 'update']);
Route::delete('roles/{id}', [RoleApiController::class, 'delete']);

Route::get('recycle-bin', [RecycleBinApiController::class, 'list']);
Route::put('recycle-bin/{deletionId}', [RecycleBinApiController::class, 'restore']);
Route::delete('recycle-bin/{deletionId}', [RecycleBinApiController::class, 'destroy']);

Route::get('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'read']);
Route::put('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'update']);
