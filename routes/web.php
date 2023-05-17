<?php

use BookStack\Activity\Controllers\AuditLogController;
use BookStack\Activity\Controllers\CommentController;
use BookStack\Activity\Controllers\FavouriteController;
use BookStack\Activity\Controllers\TagController;
use BookStack\Activity\Controllers\WebhookController;
use BookStack\Api\UserApiTokenController;
use BookStack\Entities\Controllers\BookController;
use BookStack\Entities\Controllers\BookExportController;
use BookStack\Entities\Controllers\BookshelfController;
use BookStack\Entities\Controllers\BookSortController;
use BookStack\Entities\Controllers\ChapterController;
use BookStack\Entities\Controllers\ChapterExportController;
use BookStack\Entities\Controllers\PageController;
use BookStack\Entities\Controllers\PageExportController;
use BookStack\Entities\Controllers\PageRevisionController;
use BookStack\Entities\Controllers\PageTemplateController;
use BookStack\Entities\Controllers\RecycleBinController;
use BookStack\Http\Controllers\Auth;
use BookStack\Http\Controllers\HomeController;
use BookStack\Http\Controllers\Images;
use BookStack\Http\Middleware\VerifyCsrfToken;
use BookStack\Permissions\PermissionsController;
use BookStack\References\ReferenceController;
use BookStack\Search\SearchController;
use BookStack\Settings\MaintenanceController;
use BookStack\Settings\SettingController;
use BookStack\Settings\StatusController;
use BookStack\Uploads\Controllers\AttachmentController;
use BookStack\Users\Controllers\RoleController;
use BookStack\Users\Controllers\UserController;
use BookStack\Users\Controllers\UserPreferencesController;
use BookStack\Users\Controllers\UserProfileController;
use BookStack\Users\Controllers\UserSearchController;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/status', [StatusController::class, 'show']);
Route::get('/robots.txt', [HomeController::class, 'robots']);
Route::get('/favicon.ico', [HomeController::class, 'favicon']);

// Authenticated routes...
Route::middleware('auth')->group(function () {

    // Secure images routing
    Route::get('/uploads/images/{path}', [\BookStack\Uploads\Controllers\ImageController::class, 'showImage'])
        ->where('path', '.*$');

    // API docs routes
    Route::redirect('/api', '/api/docs');
    Route::get('/api/docs', [\BookStack\Api\ApiDocsController::class, 'display']);

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
    Route::get('/shelves/{slug}/permissions', [PermissionsController::class, 'showForShelf']);
    Route::put('/shelves/{slug}/permissions', [PermissionsController::class, 'updateForShelf']);
    Route::post('/shelves/{slug}/copy-permissions', [PermissionsController::class, 'copyShelfPermissionsToBooks']);
    Route::get('/shelves/{slug}/references', [ReferenceController::class, 'shelf']);

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
    Route::get('/books/{bookSlug}/permissions', [PermissionsController::class, 'showForBook']);
    Route::put('/books/{bookSlug}/permissions', [PermissionsController::class, 'updateForBook']);
    Route::get('/books/{slug}/delete', [BookController::class, 'showDelete']);
    Route::get('/books/{bookSlug}/copy', [BookController::class, 'showCopy']);
    Route::post('/books/{bookSlug}/copy', [BookController::class, 'copy']);
    Route::post('/books/{bookSlug}/convert-to-shelf', [BookController::class, 'convertToShelf']);
    Route::get('/books/{bookSlug}/sort', [BookSortController::class, 'show']);
    Route::put('/books/{bookSlug}/sort', [BookSortController::class, 'update']);
    Route::get('/books/{slug}/references', [ReferenceController::class, 'book']);
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
    Route::get('/books/{bookSlug}/page/{pageSlug}/permissions', [PermissionsController::class, 'showForPage']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/permissions', [PermissionsController::class, 'updateForPage']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/references', [ReferenceController::class, 'page']);
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
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/copy', [ChapterController::class, 'showCopy']);
    Route::post('/books/{bookSlug}/chapter/{chapterSlug}/copy', [ChapterController::class, 'copy']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/edit', [ChapterController::class, 'edit']);
    Route::post('/books/{bookSlug}/chapter/{chapterSlug}/convert-to-book', [ChapterController::class, 'convertToBook']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/permissions', [PermissionsController::class, 'showForChapter']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/pdf', [ChapterExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/html', [ChapterExportController::class, 'html']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/markdown', [ChapterExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/plaintext', [ChapterExportController::class, 'plainText']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}/permissions', [PermissionsController::class, 'updateForChapter']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/references', [ReferenceController::class, 'chapter']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/delete', [ChapterController::class, 'showDelete']);
    Route::delete('/books/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'destroy']);

    // User Profile routes
    Route::get('/user/{slug}', [UserProfileController::class, 'show']);

    // Image routes
    Route::get('/images/gallery', [\BookStack\Uploads\Controllers\GalleryImageController::class, 'list']);
    Route::post('/images/gallery', [\BookStack\Uploads\Controllers\GalleryImageController::class, 'create']);
    Route::get('/images/drawio', [\BookStack\Uploads\Controllers\DrawioImageController::class, 'list']);
    Route::get('/images/drawio/base64/{id}', [\BookStack\Uploads\Controllers\DrawioImageController::class, 'getAsBase64']);
    Route::post('/images/drawio', [\BookStack\Uploads\Controllers\DrawioImageController::class, 'create']);
    Route::get('/images/edit/{id}', [\BookStack\Uploads\Controllers\ImageController::class, 'edit']);
    Route::put('/images/{id}', [\BookStack\Uploads\Controllers\ImageController::class, 'update']);
    Route::delete('/images/{id}', [\BookStack\Uploads\Controllers\ImageController::class, 'destroy']);

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
    Route::get('/search/entity-selector', [SearchController::class, 'searchForSelector']);
    Route::get('/search/suggest', [SearchController::class, 'searchSuggestions']);

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

    // Permissions
    Route::get('/permissions/form-row/{entityType}/{roleId}', [PermissionsController::class, 'formRowForRole']);

    // Maintenance
    Route::get('/settings/maintenance', [MaintenanceController::class, 'index']);
    Route::delete('/settings/maintenance/cleanup-images', [MaintenanceController::class, 'cleanupImages']);
    Route::post('/settings/maintenance/send-test-email', [MaintenanceController::class, 'sendTestEmail']);
    Route::post('/settings/maintenance/regenerate-references', [MaintenanceController::class, 'regenerateReferences']);

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
    Route::post('/settings/users/create', [UserController::class, 'store']);
    Route::get('/settings/users/{id}', [UserController::class, 'edit']);
    Route::put('/settings/users/{id}', [UserController::class, 'update']);
    Route::delete('/settings/users/{id}', [UserController::class, 'destroy']);

    // User Preferences
    Route::redirect('/preferences', '/');
    Route::get('/preferences/shortcuts', [UserPreferencesController::class, 'showShortcuts']);
    Route::put('/preferences/shortcuts', [UserPreferencesController::class, 'updateShortcuts']);
    Route::patch('/preferences/change-view/{type}', [UserPreferencesController::class, 'changeView']);
    Route::patch('/preferences/change-sort/{type}', [UserPreferencesController::class, 'changeSort']);
    Route::patch('/preferences/change-expansion/{type}', [UserPreferencesController::class, 'changeExpansion']);
    Route::patch('/preferences/toggle-dark-mode', [UserPreferencesController::class, 'toggleDarkMode']);
    Route::patch('/preferences/update-code-language-favourite', [UserPreferencesController::class, 'updateCodeLanguageFavourite']);
    Route::patch('/preferences/update-boolean', [UserPreferencesController::class, 'updateBooleanPreference']);

    // User API Tokens
    Route::get('/settings/users/{userId}/create-api-token', [UserApiTokenController::class, 'create']);
    Route::post('/settings/users/{userId}/create-api-token', [UserApiTokenController::class, 'store']);
    Route::get('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'edit']);
    Route::put('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'update']);
    Route::get('/settings/users/{userId}/api-tokens/{tokenId}/delete', [UserApiTokenController::class, 'delete']);
    Route::delete('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'destroy']);

    // Roles
    Route::get('/settings/roles', [RoleController::class, 'index']);
    Route::get('/settings/roles/new', [RoleController::class, 'create']);
    Route::post('/settings/roles/new', [RoleController::class, 'store']);
    Route::get('/settings/roles/delete/{id}', [RoleController::class, 'showDelete']);
    Route::delete('/settings/roles/delete/{id}', [RoleController::class, 'delete']);
    Route::get('/settings/roles/{id}', [RoleController::class, 'edit']);
    Route::put('/settings/roles/{id}', [RoleController::class, 'update']);

    // Webhooks
    Route::get('/settings/webhooks', [WebhookController::class, 'index']);
    Route::get('/settings/webhooks/create', [WebhookController::class, 'create']);
    Route::post('/settings/webhooks/create', [WebhookController::class, 'store']);
    Route::get('/settings/webhooks/{id}', [WebhookController::class, 'edit']);
    Route::put('/settings/webhooks/{id}', [WebhookController::class, 'update']);
    Route::get('/settings/webhooks/{id}/delete', [WebhookController::class, 'delete']);
    Route::delete('/settings/webhooks/{id}', [WebhookController::class, 'destroy']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings/{category}', [SettingController::class, 'category'])->name('settings.category');
    Route::post('/settings/{category}', [SettingController::class, 'update']);
});

// MFA routes
Route::middleware('mfa-setup')->group(function () {
    Route::get('/mfa/setup', [\BookStack\Access\Controllers\MfaController::class, 'setup']);
    Route::get('/mfa/totp/generate', [\BookStack\Access\Controllers\MfaTotpController::class, 'generate']);
    Route::post('/mfa/totp/confirm', [\BookStack\Access\Controllers\MfaTotpController::class, 'confirm']);
    Route::get('/mfa/backup_codes/generate', [\BookStack\Access\Controllers\MfaBackupCodesController::class, 'generate']);
    Route::post('/mfa/backup_codes/confirm', [\BookStack\Access\Controllers\MfaBackupCodesController::class, 'confirm']);
});
Route::middleware('guest')->group(function () {
    Route::get('/mfa/verify', [\BookStack\Access\Controllers\MfaController::class, 'verify']);
    Route::post('/mfa/totp/verify', [\BookStack\Access\Controllers\MfaTotpController::class, 'verify']);
    Route::post('/mfa/backup_codes/verify', [\BookStack\Access\Controllers\MfaBackupCodesController::class, 'verify']);
});
Route::delete('/mfa/{method}/remove', [\BookStack\Access\Controllers\MfaController::class, 'remove'])->middleware('auth');

// Social auth routes
Route::get('/login/service/{socialDriver}', [\BookStack\Access\Controllers\SocialController::class, 'login']);
Route::get('/login/service/{socialDriver}/callback', [\BookStack\Access\Controllers\SocialController::class, 'callback']);
Route::post('/login/service/{socialDriver}/detach', [\BookStack\Access\Controllers\SocialController::class, 'detach'])->middleware('auth');
Route::get('/register/service/{socialDriver}', [\BookStack\Access\Controllers\SocialController::class, 'register']);

// Login/Logout routes
Route::get('/login', [\BookStack\Access\Controllers\LoginController::class, 'getLogin']);
Route::post('/login', [\BookStack\Access\Controllers\LoginController::class, 'login']);
Route::post('/logout', [\BookStack\Access\Controllers\LoginController::class, 'logout']);
Route::get('/register', [\BookStack\Access\Controllers\RegisterController::class, 'getRegister']);
Route::get('/register/confirm', [\BookStack\Access\Controllers\ConfirmEmailController::class, 'show']);
Route::get('/register/confirm/awaiting', [\BookStack\Access\Controllers\ConfirmEmailController::class, 'showAwaiting']);
Route::post('/register/confirm/resend', [\BookStack\Access\Controllers\ConfirmEmailController::class, 'resend']);
Route::get('/register/confirm/{token}', [\BookStack\Access\Controllers\ConfirmEmailController::class, 'showAcceptForm']);
Route::post('/register/confirm/accept', [\BookStack\Access\Controllers\ConfirmEmailController::class, 'confirm']);
Route::post('/register', [\BookStack\Access\Controllers\RegisterController::class, 'postRegister']);

// SAML routes
Route::post('/saml2/login', [\BookStack\Access\Controllers\Saml2Controller::class, 'login']);
Route::post('/saml2/logout', [\BookStack\Access\Controllers\Saml2Controller::class, 'logout']);
Route::get('/saml2/metadata', [\BookStack\Access\Controllers\Saml2Controller::class, 'metadata']);
Route::get('/saml2/sls', [\BookStack\Access\Controllers\Saml2Controller::class, 'sls']);
Route::post('/saml2/acs', [\BookStack\Access\Controllers\Saml2Controller::class, 'startAcs'])->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
]);
Route::get('/saml2/acs', [\BookStack\Access\Controllers\Saml2Controller::class, 'processAcs']);

// OIDC routes
Route::post('/oidc/login', [\BookStack\Access\Controllers\OidcController::class, 'login']);
Route::get('/oidc/callback', [\BookStack\Access\Controllers\OidcController::class, 'callback']);

// User invitation routes
Route::get('/register/invite/{token}', [\BookStack\Access\Controllers\UserInviteController::class, 'showSetPassword']);
Route::post('/register/invite/{token}', [\BookStack\Access\Controllers\UserInviteController::class, 'setPassword']);

// Password reset link request routes
Route::get('/password/email', [\BookStack\Access\Controllers\ForgotPasswordController::class, 'showLinkRequestForm']);
Route::post('/password/email', [\BookStack\Access\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password reset routes
Route::get('/password/reset/{token}', [\BookStack\Access\Controllers\ResetPasswordController::class, 'showResetForm']);
Route::post('/password/reset', [\BookStack\Access\Controllers\ResetPasswordController::class, 'reset']);

// Metadata routes
Route::view('/help/wysiwyg', 'help.wysiwyg');

Route::fallback([HomeController::class, 'notFound'])->name('fallback');
