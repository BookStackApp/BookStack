<?php

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers all end with "ApiController"
 */

use BookStack\Activity\Controllers\AuditLogApiController;
use BookStack\Api\ApiDocsController;
use BookStack\App\SystemApiController;
use BookStack\Entities\Controllers as EntityControllers;
use BookStack\Exports\Controllers as ExportControllers;
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

Route::get('books', [EntityControllers\BookApiController::class, 'list']);
Route::post('books', [EntityControllers\BookApiController::class, 'create']);
Route::get('books/{id}', [EntityControllers\BookApiController::class, 'read']);
Route::put('books/{id}', [EntityControllers\BookApiController::class, 'update']);
Route::delete('books/{id}', [EntityControllers\BookApiController::class, 'delete']);

Route::get('books/{id}/export/html', [ExportControllers\BookExportApiController::class, 'exportHtml']);
Route::get('books/{id}/export/pdf', [ExportControllers\BookExportApiController::class, 'exportPdf']);
Route::get('books/{id}/export/plaintext', [ExportControllers\BookExportApiController::class, 'exportPlainText']);
Route::get('books/{id}/export/markdown', [ExportControllers\BookExportApiController::class, 'exportMarkdown']);
Route::get('books/{id}/export/zip', [ExportControllers\BookExportApiController::class, 'exportZip']);

Route::get('chapters', [EntityControllers\ChapterApiController::class, 'list']);
Route::post('chapters', [EntityControllers\ChapterApiController::class, 'create']);
Route::get('chapters/{id}', [EntityControllers\ChapterApiController::class, 'read']);
Route::put('chapters/{id}', [EntityControllers\ChapterApiController::class, 'update']);
Route::delete('chapters/{id}', [EntityControllers\ChapterApiController::class, 'delete']);
Route::get('chapters/{id}/export/html', [ExportControllers\ChapterExportApiController::class, 'exportHtml']);
Route::get('chapters/{id}/export/pdf', [ExportControllers\ChapterExportApiController::class, 'exportPdf']);
Route::get('chapters/{id}/export/plaintext', [ExportControllers\ChapterExportApiController::class, 'exportPlainText']);
Route::get('chapters/{id}/export/markdown', [ExportControllers\ChapterExportApiController::class, 'exportMarkdown']);
Route::get('chapters/{id}/export/zip', [ExportControllers\ChapterExportApiController::class, 'exportZip']);

Route::get('pages', [EntityControllers\PageApiController::class, 'list']);
Route::post('pages', [EntityControllers\PageApiController::class, 'create']);
Route::get('pages/{id}', [EntityControllers\PageApiController::class, 'read']);
Route::put('pages/{id}', [EntityControllers\PageApiController::class, 'update']);
Route::delete('pages/{id}', [EntityControllers\PageApiController::class, 'delete']);

Route::get('pages/{id}/export/html', [ExportControllers\PageExportApiController::class, 'exportHtml']);
Route::get('pages/{id}/export/pdf', [ExportControllers\PageExportApiController::class, 'exportPdf']);
Route::get('pages/{id}/export/plaintext', [ExportControllers\PageExportApiController::class, 'exportPlainText']);
Route::get('pages/{id}/export/markdown', [ExportControllers\PageExportApiController::class, 'exportMarkdown']);
Route::get('pages/{id}/export/zip', [ExportControllers\PageExportApiController::class, 'exportZip']);

Route::get('image-gallery', [ImageGalleryApiController::class, 'list']);
Route::post('image-gallery', [ImageGalleryApiController::class, 'create']);
Route::get('image-gallery/{id}', [ImageGalleryApiController::class, 'read']);
Route::put('image-gallery/{id}', [ImageGalleryApiController::class, 'update']);
Route::delete('image-gallery/{id}', [ImageGalleryApiController::class, 'delete']);

Route::get('search', [SearchApiController::class, 'all']);

Route::get('shelves', [EntityControllers\BookshelfApiController::class, 'list']);
Route::post('shelves', [EntityControllers\BookshelfApiController::class, 'create']);
Route::get('shelves/{id}', [EntityControllers\BookshelfApiController::class, 'read']);
Route::put('shelves/{id}', [EntityControllers\BookshelfApiController::class, 'update']);
Route::delete('shelves/{id}', [EntityControllers\BookshelfApiController::class, 'delete']);

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

Route::get('imports', [ExportControllers\ImportApiController::class, 'list']);
Route::post('imports', [ExportControllers\ImportApiController::class, 'create']);
Route::get('imports/{id}', [ExportControllers\ImportApiController::class, 'read']);
Route::post('imports/{id}', [ExportControllers\ImportApiController::class, 'run']);
Route::delete('imports/{id}', [ExportControllers\ImportApiController::class, 'delete']);

Route::get('recycle-bin', [EntityControllers\RecycleBinApiController::class, 'list']);
Route::put('recycle-bin/{deletionId}', [EntityControllers\RecycleBinApiController::class, 'restore']);
Route::delete('recycle-bin/{deletionId}', [EntityControllers\RecycleBinApiController::class, 'destroy']);

Route::get('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'read']);
Route::put('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'update']);

Route::get('audit-log', [AuditLogApiController::class, 'list']);

Route::get('system', [SystemApiController::class, 'read']);
