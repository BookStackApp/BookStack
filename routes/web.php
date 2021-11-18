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
    Route::get('/create-shelf', 'BookshelfController@create');
    Route::group(['prefix' => 'shelves'], function () {
        Route::get('/', 'BookshelfController@index');
        Route::post('/', 'BookshelfController@store');
        Route::get('/{slug}/edit', 'BookshelfController@edit');
        Route::get('/{slug}/delete', 'BookshelfController@showDelete');
        Route::get('/{slug}', 'BookshelfController@show');
        Route::put('/{slug}', 'BookshelfController@update');
        Route::delete('/{slug}', 'BookshelfController@destroy');
        Route::get('/{slug}/permissions', 'BookshelfController@showPermissions');
        Route::put('/{slug}/permissions', 'BookshelfController@permissions');
        Route::post('/{slug}/copy-permissions', 'BookshelfController@copyPermissions');

        Route::get('/{shelfSlug}/create-book', 'BookController@create');
        Route::post('/{shelfSlug}/create-book', 'BookController@store');
    });

    Route::get('/create-book', 'BookController@create');
    Route::group(['prefix' => 'books'], function () {

        // Books
        Route::get('/', 'BookController@index');
        Route::post('/', 'BookController@store');
        Route::get('/{slug}/edit', 'BookController@edit');
        Route::put('/{slug}', 'BookController@update');
        Route::delete('/{id}', 'BookController@destroy');
        Route::get('/{slug}/sort-item', 'BookSortController@showItem');
        Route::get('/{slug}', 'BookController@show');
        Route::get('/{bookSlug}/permissions', 'BookController@showPermissions');
        Route::put('/{bookSlug}/permissions', 'BookController@permissions');
        Route::get('/{slug}/delete', 'BookController@showDelete');
        Route::get('/{bookSlug}/sort', 'BookSortController@show');
        Route::put('/{bookSlug}/sort', 'BookSortController@update');
        Route::get('/{bookSlug}/export/html', 'BookExportController@html');
        Route::get('/{bookSlug}/export/pdf', 'BookExportController@pdf');
        Route::get('/{bookSlug}/export/markdown', 'BookExportController@markdown');
        Route::get('/{bookSlug}/export/zip', 'BookExportController@zip');
        Route::get('/{bookSlug}/export/plaintext', 'BookExportController@plainText');

        // Pages
        Route::get('/{bookSlug}/create-page', 'PageController@create');
        Route::post('/{bookSlug}/create-guest-page', 'PageController@createAsGuest');
        Route::get('/{bookSlug}/draft/{pageId}', 'PageController@editDraft');
        Route::post('/{bookSlug}/draft/{pageId}', 'PageController@store');
        Route::get('/{bookSlug}/page/{pageSlug}', 'PageController@show');
        Route::get('/{bookSlug}/page/{pageSlug}/export/pdf', 'PageExportController@pdf');
        Route::get('/{bookSlug}/page/{pageSlug}/export/html', 'PageExportController@html');
        Route::get('/{bookSlug}/page/{pageSlug}/export/markdown', 'PageExportController@markdown');
        Route::get('/{bookSlug}/page/{pageSlug}/export/plaintext', 'PageExportController@plainText');
        Route::get('/{bookSlug}/page/{pageSlug}/edit', 'PageController@edit');
        Route::get('/{bookSlug}/page/{pageSlug}/move', 'PageController@showMove');
        Route::put('/{bookSlug}/page/{pageSlug}/move', 'PageController@move');
        Route::get('/{bookSlug}/page/{pageSlug}/copy', 'PageController@showCopy');
        Route::post('/{bookSlug}/page/{pageSlug}/copy', 'PageController@copy');
        Route::get('/{bookSlug}/page/{pageSlug}/delete', 'PageController@showDelete');
        Route::get('/{bookSlug}/draft/{pageId}/delete', 'PageController@showDeleteDraft');
        Route::get('/{bookSlug}/page/{pageSlug}/permissions', 'PageController@showPermissions');
        Route::put('/{bookSlug}/page/{pageSlug}/permissions', 'PageController@permissions');
        Route::put('/{bookSlug}/page/{pageSlug}', 'PageController@update');
        Route::delete('/{bookSlug}/page/{pageSlug}', 'PageController@destroy');
        Route::delete('/{bookSlug}/draft/{pageId}', 'PageController@destroyDraft');

        // Revisions
        Route::get('/{bookSlug}/page/{pageSlug}/revisions', 'PageRevisionController@index');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}', 'PageRevisionController@show');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/changes', 'PageRevisionController@changes');
        Route::put('/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', 'PageRevisionController@restore');
        Route::delete('/{bookSlug}/page/{pageSlug}/revisions/{revId}/delete', 'PageRevisionController@destroy');

        // Chapters
        Route::get('/{bookSlug}/chapter/{chapterSlug}/create-page', 'PageController@create');
        Route::post('/{bookSlug}/chapter/{chapterSlug}/create-guest-page', 'PageController@createAsGuest');
        Route::get('/{bookSlug}/create-chapter', 'ChapterController@create');
        Route::post('/{bookSlug}/create-chapter', 'ChapterController@store');
        Route::get('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@show');
        Route::put('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@update');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/move', 'ChapterController@showMove');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/move', 'ChapterController@move');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/edit', 'ChapterController@edit');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@showPermissions');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/pdf', 'ChapterExportController@pdf');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/html', 'ChapterExportController@html');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/markdown', 'ChapterExportController@markdown');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/plaintext', 'ChapterExportController@plainText');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@permissions');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/delete', 'ChapterController@showDelete');
        Route::delete('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@destroy');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/sort', 'ChapterSortController@show');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/sort-item', 'ChapterSortController@showItem');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/sort', 'ChapterSortController@update');
    });

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
Route::post('/logout', [Auth\LoginController::class, 'logout']);
Route::get('/register', [Auth\RegisterController::class, 'getRegister']);
Route::get('/register/confirm', [Auth\ConfirmEmailController::class, 'show']);
Route::get('/register/confirm/awaiting', [Auth\ConfirmEmailController::class, 'showAwaiting']);
Route::post('/register/confirm/resend', [Auth\ConfirmEmailController::class, 'resend']);
Route::get('/register/confirm/{token}', [Auth\ConfirmEmailController::class, 'confirm']);
Route::post('/register', [Auth\RegisterController::class, 'postRegister']);

// SAML routes
Route::post('/saml2/login', [Auth\Saml2Controller::class, 'login']);
Route::post('/saml2/logout', [Auth\Saml2Controller::class, 'logout']);
Route::get('/saml2/metadata', [Auth\Saml2Controller::class, 'metadata']);
Route::get('/saml2/sls', [Auth\Saml2Controller::class, 'sls']);
Route::post('/saml2/acs', [Auth\Saml2Controller::class, 'startAcs'])->withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \BookStack\Http\Middleware\VerifyCsrfToken::class,
]);
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
