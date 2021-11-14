<?php

use BookStack\Http\Controllers\Api;
use BookStack\Http\Controllers\AttachmentController;
use BookStack\Http\Controllers\AuditLogController;
use BookStack\Http\Controllers\Auth;
use BookStack\Http\Controllers\BookController;
use BookStack\Http\Controllers\BookExportController;
use BookStack\Http\Controllers\BookshelfController;
use BookStack\Http\Controllers\BookSortController;
use BookStack\Http\Controllers\ChapterController;
use BookStack\Http\Controllers\ChapterExportController;
use BookStack\Http\Controllers\CommentController;
use BookStack\Http\Controllers\FavouriteController;
use BookStack\Http\Controllers\HomeController;
use BookStack\Http\Controllers\Images;
use BookStack\Http\Controllers\MaintenanceController;
use BookStack\Http\Controllers\PageController;
use BookStack\Http\Controllers\PageExportController;
use BookStack\Http\Controllers\PageRevisionController;
use BookStack\Http\Controllers\PageTemplateController;
use BookStack\Http\Controllers\RecycleBinController;
use BookStack\Http\Controllers\RoleController;
use BookStack\Http\Controllers\SearchController;
use BookStack\Http\Controllers\SettingController;
use BookStack\Http\Controllers\StatusController;
use BookStack\Http\Controllers\TagController;
use BookStack\Http\Controllers\UserApiTokenController;
use BookStack\Http\Controllers\UserController;
use BookStack\Http\Controllers\UserProfileController;
use BookStack\Http\Controllers\UserSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/status', [StatusController::class, 'show']);
Route::get('/robots.txt', [HomeController::class, 'robots']);

// Authenticated routes...
Route::middleware('auth')->group(function () {

    // Secure images routing
    Route::get('/uploads/images/{path}', [Images\ImageController::class, 'showImage'])
        ->where('path', '.*$');

    // API docs routes
    Route::redirect('/api', '/api/docs');
    Route::get('/api/docs', [Api\ApiDocsController::class, 'display']);

    Route::get('/pages/recently-updated', [PageController::class, 'showRecentlyUpdated']);

    // Shelves
    Route::get('/create-shelf', [BookshelfController::class, 'create']);
    Route::get('/shelves/', [BookshelfController::class, 'index']);
    Route::post('/shelves/', [BookshelfController::class, 'store']);
    Route::get('/shelves/{slug}/edit', [BookshelfController::class, 'edit']);
    Route::get('/shelves/{slug}/delete', [BookshelfController::class, 'showDelete']);
    Route::get('/shelves/{slug}', [BookshelfController::class, 'show']);
    Route::put('/shelves/{slug}', [BookshelfController::class, 'update']);
    Route::delete('/shelves/{slug}', [BookshelfController::class, 'destroy']);
    Route::get('/shelves/{slug}/permissions', [BookshelfController::class, 'showPermissions']);
    Route::put('/shelves/{slug}/permissions', [BookshelfController::class, 'permissions']);
    Route::post('/shelves/{slug}/copy-permissions', [BookshelfController::class, 'copyPermissions']);

    // Book Creation
    Route::get('/shelves/{shelfSlug}/create-book', [BookController::class, 'create']);
    Route::post('/shelves/{shelfSlug}/create-book', [BookController::class, 'store']);
    Route::get('/create-book', [BookController::class, 'create']);

    // Books
    Route::get('/books/', [BookController::class, 'index']);
    Route::post('/books/', [BookController::class, 'store']);
    Route::get('/books/{slug}/edit', [BookController::class, 'edit']);
    Route::put('/books/{slug}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);
    Route::get('/books/{slug}/sort-item', [BookSortController::class, 'showItem']);
    Route::get('/books/{slug}', [BookController::class, 'show']);
    Route::get('/books/{bookSlug}/permissions', [BookController::class, 'showPermissions']);
    Route::put('/books/{bookSlug}/permissions', [BookController::class, 'permissions']);
    Route::get('/books/{slug}/delete', [BookController::class, 'showDelete']);
    Route::get('/books/{bookSlug}/sort', [BookSortController::class, 'show']);
    Route::put('/books/{bookSlug}/sort', [BookSortController::class, 'update']);
    Route::get('/books/{bookSlug}/export/html', [BookExportController::class, 'html']);
    Route::get('/books/{bookSlug}/export/pdf', [BookExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/export/markdown', [BookExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/export/zip', [BookExportController::class, 'zip']);
    Route::get('/books/{bookSlug}/export/plaintext', [BookExportController::class, 'plainText']);

    // Pages
    Route::get('/books/{bookSlug}/create-page', [PageController::class, 'create']);
    Route::post('/books/{bookSlug}/create-guest-page', [PageController::class, 'createAsGuest']);
    Route::get('/books/{bookSlug}/draft/{pageId}', [PageController::class, 'editDraft']);
    Route::post('/books/{bookSlug}/draft/{pageId}', [PageController::class, 'store']);
    Route::get('/books/{bookSlug}/page/{pageSlug}', [PageController::class, 'show']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/pdf', [PageExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/html', [PageExportController::class, 'html']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/markdown', [PageExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/plaintext', [PageExportController::class, 'plainText']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/edit', [PageController::class, 'edit']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/move', [PageController::class, 'showMove']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/move', [PageController::class, 'move']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/copy', [PageController::class, 'showCopy']);
    Route::post('/books/{bookSlug}/page/{pageSlug}/copy', [PageController::class, 'copy']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/delete', [PageController::class, 'showDelete']);
    Route::get('/books/{bookSlug}/draft/{pageId}/delete', [PageController::class, 'showDeleteDraft']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/permissions', [PageController::class, 'showPermissions']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/permissions', [PageController::class, 'permissions']);
    Route::put('/books/{bookSlug}/page/{pageSlug}', [PageController::class, 'update']);
    Route::delete('/books/{bookSlug}/page/{pageSlug}', [PageController::class, 'destroy']);
    Route::delete('/books/{bookSlug}/draft/{pageId}', [PageController::class, 'destroyDraft']);

    // Revisions
    Route::get('/books/{bookSlug}/page/{pageSlug}/revisions', [PageRevisionController::class, 'index']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}', [PageRevisionController::class, 'show']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}/changes', [PageRevisionController::class, 'changes']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', [PageRevisionController::class, 'restore']);
    Route::delete('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}/delete', [PageRevisionController::class, 'destroy']);

    // Chapters
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/create-page', [PageController::class, 'create']);
    Route::post('/books/{bookSlug}/chapter/{chapterSlug}/create-guest-page', [PageController::class, 'createAsGuest']);
    Route::get('/books/{bookSlug}/create-chapter', [ChapterController::class, 'create']);
    Route::post('/books/{bookSlug}/create-chapter', [ChapterController::class, 'store']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'show']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'update']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/move', [ChapterController::class, 'showMove']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}/move', [ChapterController::class, 'move']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/edit', [ChapterController::class, 'edit']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/permissions', [ChapterController::class, 'showPermissions']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/pdf', [ChapterExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/html', [ChapterExportController::class, 'html']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/markdown', [ChapterExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/plaintext', [ChapterExportController::class, 'plainText']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}/permissions', [ChapterController::class, 'permissions']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/delete', [ChapterController::class, 'showDelete']);
    Route::delete('/books/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'destroy']);

    // User Profile routes
    Route::get('/user/{slug}', [UserProfileController::class, 'show']);

    // Image routes
    Route::get('/images/gallery', [Images\GalleryImageController::class, 'list']);
    Route::post('/images/gallery', [Images\GalleryImageController::class, 'create']);
    Route::get('/images/drawio', [Images\DrawioImageController::class, 'list']);
    Route::get('/images/drawio/base64/{id}', [Images\DrawioImageController::class, 'getAsBase64']);
    Route::post('/images/drawio', [Images\DrawioImageController::class, 'create']);
    Route::get('/images/edit/{id}', [Images\ImageController::class, 'edit']);
    Route::put('/images/{id}', [Images\ImageController::class, 'update']);
    Route::delete('/images/{id}', [Images\ImageController::class, 'destroy']);

    // Attachments routes
    Route::get('/attachments/{id}', [AttachmentController::class, 'get']);
    Route::post('/attachments/upload', [AttachmentController::class, 'upload']);
    Route::post('/attachments/upload/{id}', [AttachmentController::class, 'uploadUpdate']);
    Route::post('/attachments/link', [AttachmentController::class, 'attachLink']);
    Route::put('/attachments/{id}', [AttachmentController::class, 'update']);
    Route::get('/attachments/edit/{id}', [AttachmentController::class, 'getUpdateForm']);
    Route::get('/attachments/get/page/{pageId}', [AttachmentController::class, 'listForPage']);
    Route::put('/attachments/sort/page/{pageId}', [AttachmentController::class, 'sortForPage']);
    Route::delete('/attachments/{id}', [AttachmentController::class, 'delete']);

    // AJAX routes
    Route::put('/ajax/page/{id}/save-draft', [PageController::class, 'saveDraft']);
    Route::get('/ajax/page/{id}', [PageController::class, 'getPageAjax']);
    Route::delete('/ajax/page/{id}', [PageController::class, 'ajaxDestroy']);

    // Tag routes
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/ajax/tags/suggest/names', [TagController::class, 'getNameSuggestions']);
    Route::get('/ajax/tags/suggest/values', [TagController::class, 'getValueSuggestions']);

    Route::get('/ajax/search/entities', [SearchController::class, 'searchEntitiesAjax']);

    // Comments
    Route::post('/comment/{pageId}', [CommentController::class, 'savePageComment']);
    Route::put('/comment/{id}', [CommentController::class, 'update']);
    Route::delete('/comment/{id}', [CommentController::class, 'destroy']);

    // Links
    Route::get('/link/{id}', [PageController::class, 'redirectFromLink']);

    // Search
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/book/{bookId}', [SearchController::class, 'searchBook']);
    Route::get('/search/chapter/{bookId}', [SearchController::class, 'searchChapter']);
    Route::get('/search/entity/siblings', [SearchController::class, 'searchSiblings']);

    // User Search
    Route::get('/search/users/select', [UserSearchController::class, 'forSelect']);

    // Template System
    Route::get('/templates', [PageTemplateController::class, 'list']);
    Route::get('/templates/{templateId}', [PageTemplateController::class, 'get']);

    // Favourites
    Route::get('/favourites', [FavouriteController::class, 'index']);
    Route::post('/favourites/add', [FavouriteController::class, 'add']);
    Route::post('/favourites/remove', [FavouriteController::class, 'remove']);

    // Other Pages
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/custom-head-content', [HomeController::class, 'customHeadContent']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update']);

    // Maintenance
    Route::get('/settings/maintenance', [MaintenanceController::class, 'index']);
    Route::delete('/settings/maintenance/cleanup-images', [MaintenanceController::class, 'cleanupImages']);
    Route::post('/settings/maintenance/send-test-email', [MaintenanceController::class, 'sendTestEmail']);

    // Recycle Bin
    Route::get('/settings/recycle-bin', [RecycleBinController::class, 'index']);
    Route::post('/settings/recycle-bin/empty', [RecycleBinController::class, 'empty']);
    Route::get('/settings/recycle-bin/{id}/destroy', [RecycleBinController::class, 'showDestroy']);
    Route::delete('/settings/recycle-bin/{id}', [RecycleBinController::class, 'destroy']);
    Route::get('/settings/recycle-bin/{id}/restore', [RecycleBinController::class, 'showRestore']);
    Route::post('/settings/recycle-bin/{id}/restore', [RecycleBinController::class, 'restore']);

    // Audit Log
    Route::get('/settings/audit', [AuditLogController::class, 'index']);

    // Users
    Route::get('/settings/users', [UserController::class, 'index']);
    Route::get('/settings/users/create', [UserController::class, 'create']);
    Route::get('/settings/users/{id}/delete', [UserController::class, 'delete']);
    Route::patch('/settings/users/{id}/switch-books-view', [UserController::class, 'switchBooksView']);
    Route::patch('/settings/users/{id}/switch-shelves-view', [UserController::class, 'switchShelvesView']);
    Route::patch('/settings/users/{id}/switch-shelf-view', [UserController::class, 'switchShelfView']);
    Route::patch('/settings/users/{id}/change-sort/{type}', [UserController::class, 'changeSort']);
    Route::patch('/settings/users/{id}/update-expansion-preference/{key}', [UserController::class, 'updateExpansionPreference']);
    Route::patch('/settings/users/toggle-dark-mode', [UserController::class, 'toggleDarkMode']);
    Route::post('/settings/users/create', [UserController::class, 'store']);
    Route::get('/settings/users/{id}', [UserController::class, 'edit']);
    Route::put('/settings/users/{id}', [UserController::class, 'update']);
    Route::delete('/settings/users/{id}', [UserController::class, 'destroy']);

    // User API Tokens
    Route::get('/settings/users/{userId}/create-api-token', [UserApiTokenController::class, 'create']);
    Route::post('/settings/users/{userId}/create-api-token', [UserApiTokenController::class, 'store']);
    Route::get('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'edit']);
    Route::put('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'update']);
    Route::get('/settings/users/{userId}/api-tokens/{tokenId}/delete', [UserApiTokenController::class, 'delete']);
    Route::delete('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'destroy']);

    // Roles
    Route::get('/settings/roles', [RoleController::class, 'list']);
    Route::get('/settings/roles/new', [RoleController::class, 'create']);
    Route::post('/settings/roles/new', [RoleController::class, 'store']);
    Route::get('/settings/roles/delete/{id}', [RoleController::class, 'showDelete']);
    Route::delete('/settings/roles/delete/{id}', [RoleController::class, 'delete']);
    Route::get('/settings/roles/{id}', [RoleController::class, 'edit']);
    Route::put('/settings/roles/{id}', [RoleController::class, 'update']);
});

// MFA routes
Route::middleware('mfa-setup')->group(function () {
    Route::get('/mfa/setup', [Auth\MfaController::class, 'setup']);
    Route::get('/mfa/totp/generate', [Auth\MfaTotpController::class, 'generate']);
    Route::post('/mfa/totp/confirm', [Auth\MfaTotpController::class, 'confirm']);
    Route::get('/mfa/backup_codes/generate', [Auth\MfaBackupCodesController::class, 'generate']);
    Route::post('/mfa/backup_codes/confirm', [Auth\MfaBackupCodesController::class, 'confirm']);
});
Route::middleware('guest')->group(function () {
    Route::get('/mfa/verify', [Auth\MfaController::class, 'verify']);
    Route::post('/mfa/totp/verify', [Auth\MfaTotpController::class, 'verify']);
    Route::post('/mfa/backup_codes/verify', [Auth\MfaBackupCodesController::class, 'verify']);
});
Route::delete('/mfa/{method}/remove', [Auth\MfaController::class, 'remove'])->middleware('auth');

// Social auth routes
Route::get('/login/service/{socialDriver}', [Auth\SocialController::class, 'login']);
Route::get('/login/service/{socialDriver}/callback', [Auth\SocialController::class, 'callback']);
Route::post('/login/service/{socialDriver}/detach', [Auth\SocialController::class, 'detach'])->middleware('auth');
Route::get('/register/service/{socialDriver}', [Auth\SocialController::class, 'register']);

// Login/Logout routes
Route::get('/login', [Auth\LoginController::class, 'getLogin']);
Route::post('/login', [Auth\LoginController::class, 'login']);
Route::get('/logout', [Auth\LoginController::class, 'logout']);
Route::get('/register', [Auth\RegisterController::class, 'getRegister']);
Route::get('/register/confirm', [Auth\ConfirmEmailController::class, 'show']);
Route::get('/register/confirm/awaiting', [Auth\ConfirmEmailController::class, 'showAwaiting']);
Route::post('/register/confirm/resend', [Auth\ConfirmEmailController::class, 'resend']);
Route::get('/register/confirm/{token}', [Auth\ConfirmEmailController::class, 'confirm']);
Route::post('/register', [Auth\RegisterController::class, 'postRegister']);

// SAML routes
Route::post('/saml2/login', [Auth\Saml2Controller::class, 'login']);
Route::get('/saml2/logout', [Auth\Saml2Controller::class, 'logout']);
Route::get('/saml2/metadata', [Auth\Saml2Controller::class, 'metadata']);
Route::get('/saml2/sls', [Auth\Saml2Controller::class, 'sls']);
Route::post('/saml2/acs', [Auth\Saml2Controller::class, 'startAcs']);
Route::get('/saml2/acs', [Auth\Saml2Controller::class, 'processAcs']);

// OIDC routes
Route::post('/oidc/login', [Auth\OidcController::class, 'login']);
Route::get('/oidc/callback', [Auth\OidcController::class, 'callback']);

// User invitation routes
Route::get('/register/invite/{token}', [Auth\UserInviteController::class, 'showSetPassword']);
Route::post('/register/invite/{token}', [Auth\UserInviteController::class, 'setPassword']);

// Password reset link request routes...
Route::get('/password/email', [Auth\ForgotPasswordController::class, 'showLinkRequestForm']);
Route::post('/password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password reset routes...
Route::get('/password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm']);
Route::post('/password/reset', [Auth\ResetPasswordController::class, 'reset']);

Route::fallback([HomeController::class, 'notFound'])->name('fallback');
