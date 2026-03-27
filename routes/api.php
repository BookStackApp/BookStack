<?php

/**
 * Routes for the BookStack API.
 * Routes have a URI prefix of /api/.
 * Controllers all end with "ApiController"
 *
 * Permission Middleware Notes:
 * - Create operations use generic 'create' permission which is checked at route level
 * - Update/Delete operations are entity-specific and checked in controllers
 * - List/Read operations rely on entity visibility (checked in queries)
 * - Admin/system endpoints require specific permissions via middleware
 */

use BookStack\Activity\Controllers as ActivityControllers;
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

// ============================================================================
// Pages
// ============================================================================

Route::get('pages', [EntityControllers\PageApiController::class, 'list']);
Route::post('pages', [EntityControllers\PageApiController::class, 'create'])
    ->middleware('can:page-create');
Route::get('pages/{id}', [EntityControllers\PageApiController::class, 'read']);
Route::put('pages/{id}', [EntityControllers\PageApiController::class, 'update']);
Route::delete('pages/{id}', [EntityControllers\PageApiController::class, 'delete']);
Route::get('pages/{id}/export/html', [ExportControllers\PageExportApiController::class, 'exportHtml'])
    ->middleware('can:content-export');
Route::get('pages/{id}/export/pdf', [ExportControllers\PageExportApiController::class, 'exportPdf'])
    ->middleware('can:content-export');
Route::get('pages/{id}/export/plaintext', [ExportControllers\PageExportApiController::class, 'exportPlainText'])
    ->middleware('can:content-export');
Route::get('pages/{id}/export/markdown', [ExportControllers\PageExportApiController::class, 'exportMarkdown'])
    ->middleware('can:content-export');
Route::get('pages/{id}/export/zip', [ExportControllers\PageExportApiController::class, 'exportZip'])
    ->middleware('can:content-export');

// ============================================================================
// Chapters
// ============================================================================

Route::get('chapters', [EntityControllers\ChapterApiController::class, 'list']);
Route::post('chapters', [EntityControllers\ChapterApiController::class, 'create'])
    ->middleware('can:chapter-create');
Route::get('chapters/{id}', [EntityControllers\ChapterApiController::class, 'read']);
Route::put('chapters/{id}', [EntityControllers\ChapterApiController::class, 'update']);
Route::delete('chapters/{id}', [EntityControllers\ChapterApiController::class, 'delete']);
Route::get('chapters/{id}/export/html', [ExportControllers\ChapterExportApiController::class, 'exportHtml'])
    ->middleware('can:content-export');
Route::get('chapters/{id}/export/pdf', [ExportControllers\ChapterExportApiController::class, 'exportPdf'])
    ->middleware('can:content-export');
Route::get('chapters/{id}/export/plaintext', [ExportControllers\ChapterExportApiController::class, 'exportPlainText'])
    ->middleware('can:content-export');
Route::get('chapters/{id}/export/markdown', [ExportControllers\ChapterExportApiController::class, 'exportMarkdown'])
    ->middleware('can:content-export');
Route::get('chapters/{id}/export/zip', [ExportControllers\ChapterExportApiController::class, 'exportZip'])
    ->middleware('can:content-export');

// ============================================================================
// Books
// ============================================================================

Route::get('books', [EntityControllers\BookApiController::class, 'list']);
Route::post('books', [EntityControllers\BookApiController::class, 'create'])
    ->middleware('can:book-create');
Route::get('books/{id}', [EntityControllers\BookApiController::class, 'read']);
Route::put('books/{id}', [EntityControllers\BookApiController::class, 'update']);
Route::delete('books/{id}', [EntityControllers\BookApiController::class, 'delete']);
Route::get('books/{id}/export/html', [ExportControllers\BookExportApiController::class, 'exportHtml'])
    ->middleware('can:content-export');
Route::get('books/{id}/export/pdf', [ExportControllers\BookExportApiController::class, 'exportPdf'])
    ->middleware('can:content-export');
Route::get('books/{id}/export/plaintext', [ExportControllers\BookExportApiController::class, 'exportPlainText'])
    ->middleware('can:content-export');
Route::get('books/{id}/export/markdown', [ExportControllers\BookExportApiController::class, 'exportMarkdown'])
    ->middleware('can:content-export');
Route::get('books/{id}/export/zip', [ExportControllers\BookExportApiController::class, 'exportZip'])
    ->middleware('can:content-export');

// ============================================================================
// Shelves
// ============================================================================

Route::get('shelves', [EntityControllers\BookshelfApiController::class, 'list']);
Route::post('shelves', [EntityControllers\BookshelfApiController::class, 'create'])
    ->middleware('can:bookshelf-create');
Route::get('shelves/{id}', [EntityControllers\BookshelfApiController::class, 'read']);
Route::put('shelves/{id}', [EntityControllers\BookshelfApiController::class, 'update']);
Route::delete('shelves/{id}', [EntityControllers\BookshelfApiController::class, 'delete']);

// ============================================================================
// Attachments
// ============================================================================

Route::get('attachments', [AttachmentApiController::class, 'list']);
Route::post('attachments', [AttachmentApiController::class, 'create'])
    ->middleware('can:attachment-create');
Route::get('attachments/{id}', [AttachmentApiController::class, 'read']);
Route::put('attachments/{id}', [AttachmentApiController::class, 'update']);
Route::delete('attachments/{id}', [AttachmentApiController::class, 'delete']);

// ============================================================================
// Audit Log (Admin)
// ============================================================================

Route::get('audit-log', [ActivityControllers\AuditLogApiController::class, 'list'])
    ->middleware('can:settings-manage');

// ============================================================================
// Comments
// ============================================================================

Route::get('comments', [ActivityControllers\CommentApiController::class, 'list']);
Route::post('comments', [ActivityControllers\CommentApiController::class, 'create'])
    ->middleware('can:comment-create');
Route::get('comments/{id}', [ActivityControllers\CommentApiController::class, 'read']);
Route::put('comments/{id}', [ActivityControllers\CommentApiController::class, 'update']);
Route::delete('comments/{id}', [ActivityControllers\CommentApiController::class, 'delete']);

// ============================================================================
// Content Permissions (Admin)
// ============================================================================

Route::get('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'read'])
    ->middleware('can:restrictions-manage');
Route::put('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'update'])
    ->middleware('can:restrictions-manage');

// ============================================================================
// API Documentation
// ============================================================================

Route::get('docs.json', [ApiDocsController::class, 'json']);

// ============================================================================
// Image Gallery
// ============================================================================

Route::get('image-gallery', [ImageGalleryApiController::class, 'list']);
Route::post('image-gallery', [ImageGalleryApiController::class, 'create'])
    ->middleware('can:image-create');
Route::get('image-gallery/url/data', [ImageGalleryApiController::class, 'readDataForUrl']);
Route::get('image-gallery/{id}', [ImageGalleryApiController::class, 'read']);
Route::get('image-gallery/{id}/data', [ImageGalleryApiController::class, 'readData']);
Route::put('image-gallery/{id}', [ImageGalleryApiController::class, 'update']);
Route::delete('image-gallery/{id}', [ImageGalleryApiController::class, 'delete']);

// ============================================================================
// Imports
// ============================================================================

Route::get('imports', [ExportControllers\ImportApiController::class, 'list'])
    ->middleware('can:content-import');
Route::post('imports', [ExportControllers\ImportApiController::class, 'create'])
    ->middleware('can:content-import');
Route::get('imports/{id}', [ExportControllers\ImportApiController::class, 'read']);
Route::post('imports/{id}', [ExportControllers\ImportApiController::class, 'run'])
    ->middleware('can:content-import');
Route::delete('imports/{id}', [ExportControllers\ImportApiController::class, 'delete']);

// ============================================================================
// Recycle Bin (Admin)
// ============================================================================

Route::get('recycle-bin', [EntityControllers\RecycleBinApiController::class, 'list'])
    ->middleware('can:settings-manage');
Route::put('recycle-bin/{deletionId}', [EntityControllers\RecycleBinApiController::class, 'restore'])
    ->middleware('can:settings-manage');
Route::delete('recycle-bin/{deletionId}', [EntityControllers\RecycleBinApiController::class, 'destroy'])
    ->middleware('can:settings-manage');

// ============================================================================
// Roles (Admin)
// ============================================================================

Route::get('roles', [RoleApiController::class, 'list'])
    ->middleware('can:user-roles-manage');
Route::post('roles', [RoleApiController::class, 'create'])
    ->middleware('can:user-roles-manage');
Route::get('roles/{id}', [RoleApiController::class, 'read'])
    ->middleware('can:user-roles-manage');
Route::put('roles/{id}', [RoleApiController::class, 'update'])
    ->middleware('can:user-roles-manage');
Route::delete('roles/{id}', [RoleApiController::class, 'delete'])
    ->middleware('can:user-roles-manage');

// ============================================================================
// Search
// ============================================================================

Route::get('search', [SearchApiController::class, 'all']);

// ============================================================================
// System (Admin)
// ============================================================================

Route::get('system', [SystemApiController::class, 'read'])
    ->middleware('can:settings-manage');

// ============================================================================
// Users (Admin)
// ============================================================================

Route::get('users', [UserApiController::class, 'list'])
    ->middleware('can:users-manage');
Route::post('users', [UserApiController::class, 'create'])
    ->middleware('can:users-manage');
Route::get('users/{id}', [UserApiController::class, 'read'])
    ->middleware('can:users-manage');
Route::put('users/{id}', [UserApiController::class, 'update'])
    ->middleware('can:users-manage');
Route::delete('users/{id}', [UserApiController::class, 'delete'])
    ->middleware('can:users-manage');
