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
use BookStack\Permissions\ContentPermissionApiController;
use BookStack\Search\SearchApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('attachments')->group(
    base_path('routes/api/attachments.php')
);

Route::prefix('books')->group(
    base_path('routes/api/books.php')
);

Route::prefix('chapters')->group(
    base_path('routes/api/chapters.php')
);

Route::prefix('pages')->group(
    base_path('routes/api/pages.php')
);

Route::prefix('image-gallery')->group(
    base_path('routes/api/image-gallery.php')
);

Route::prefix('shelves')->group(
    base_path('routes/api/shelves.php')
);

Route::prefix('users')->group(
    base_path('routes/api/users.php')
);

Route::prefix('roles')->group(
    base_path('routes/api/roles.php')
);

Route::get('docs.json', [ApiDocsController::class, 'json']);

Route::get('recycle-bin', [EntityControllers\RecycleBinApiController::class, 'list']);
Route::put('recycle-bin/{deletionId}', [EntityControllers\RecycleBinApiController::class, 'restore']);
Route::delete('recycle-bin/{deletionId}', [EntityControllers\RecycleBinApiController::class, 'destroy']);

Route::get('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'read']);
Route::put('content-permissions/{contentType}/{contentId}', [ContentPermissionApiController::class, 'update']);

Route::get('audit-log', [AuditLogApiController::class, 'list']);

Route::get('system', [SystemApiController::class, 'read']);

Route::get('search', [SearchApiController::class, 'all']);
