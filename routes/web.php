<?php

use BookStack\Access\Controllers as AccessControllers;
use BookStack\Activity\Controllers as ActivityControllers;
use BookStack\Api\ApiDocsController;
use BookStack\Api\UserApiTokenController;
use BookStack\App\HomeController;
use BookStack\Entities\Controllers as EntityControllers;
use BookStack\Http\Middleware\VerifyCsrfToken;
use BookStack\Permissions\PermissionsController;
use BookStack\References\ReferenceController;
use BookStack\Search\SearchController;
use BookStack\Settings as SettingControllers;
use BookStack\Uploads\Controllers as UploadControllers;
use BookStack\Users\Controllers as UserControllers;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/status', [SettingControllers\StatusController::class, 'show']);
Route::get('/robots.txt', [HomeController::class, 'robots']);
Route::get('/favicon.ico', [HomeController::class, 'favicon']);
Route::get('/manifest.json', [HomeController::class, 'pwaManifest']);

// Authenticated routes...
Route::middleware('auth')->group(function () {

    // Secure images routing
    Route::get('/uploads/images/{path}', [UploadControllers\ImageController::class, 'showImage'])
        ->where('path', '.*$');

    // API docs routes
    Route::get('/api', [ApiDocsController::class, 'redirect']);
    Route::get('/api/docs', [ApiDocsController::class, 'display']);

    Route::get('/pages/recently-updated', [EntityControllers\PageController::class, 'showRecentlyUpdated']);

    // Shelves
    Route::get('/create-shelf', [EntityControllers\BookshelfController::class, 'create']);
    Route::get('/shelves/', [EntityControllers\BookshelfController::class, 'index']);
    Route::post('/shelves/', [EntityControllers\BookshelfController::class, 'store']);
    Route::get('/shelves/{slug}/edit', [EntityControllers\BookshelfController::class, 'edit']);
    Route::get('/shelves/{slug}/delete', [EntityControllers\BookshelfController::class, 'showDelete']);
    Route::get('/shelves/{slug}', [EntityControllers\BookshelfController::class, 'show']);
    Route::put('/shelves/{slug}', [EntityControllers\BookshelfController::class, 'update']);
    Route::delete('/shelves/{slug}', [EntityControllers\BookshelfController::class, 'destroy']);
    Route::get('/shelves/{slug}/permissions', [PermissionsController::class, 'showForShelf']);
    Route::put('/shelves/{slug}/permissions', [PermissionsController::class, 'updateForShelf']);
    Route::post('/shelves/{slug}/copy-permissions', [PermissionsController::class, 'copyShelfPermissionsToBooks']);
    Route::get('/shelves/{slug}/references', [ReferenceController::class, 'shelf']);

    // Book Creation
    Route::get('/shelves/{shelfSlug}/create-book', [EntityControllers\BookController::class, 'create']);
    Route::post('/shelves/{shelfSlug}/create-book', [EntityControllers\BookController::class, 'store']);
    Route::get('/create-book', [EntityControllers\BookController::class, 'create']);

    // Books
    Route::get('/books/', [EntityControllers\BookController::class, 'index']);
    Route::post('/books/', [EntityControllers\BookController::class, 'store']);
    Route::get('/books/{slug}/edit', [EntityControllers\BookController::class, 'edit']);
    Route::put('/books/{slug}', [EntityControllers\BookController::class, 'update']);
    Route::delete('/books/{id}', [EntityControllers\BookController::class, 'destroy']);
    Route::get('/books/{slug}/sort-item', [EntityControllers\BookSortController::class, 'showItem']);
    Route::get('/books/{slug}', [EntityControllers\BookController::class, 'show']);
    Route::get('/books/{bookSlug}/permissions', [PermissionsController::class, 'showForBook']);
    Route::put('/books/{bookSlug}/permissions', [PermissionsController::class, 'updateForBook']);
    Route::get('/books/{slug}/delete', [EntityControllers\BookController::class, 'showDelete']);
    Route::get('/books/{bookSlug}/copy', [EntityControllers\BookController::class, 'showCopy']);
    Route::post('/books/{bookSlug}/copy', [EntityControllers\BookController::class, 'copy']);
    Route::post('/books/{bookSlug}/convert-to-shelf', [EntityControllers\BookController::class, 'convertToShelf']);
    Route::get('/books/{bookSlug}/sort', [EntityControllers\BookSortController::class, 'show']);
    Route::put('/books/{bookSlug}/sort', [EntityControllers\BookSortController::class, 'update']);
    Route::get('/books/{slug}/references', [ReferenceController::class, 'book']);
    Route::get('/books/{bookSlug}/export/html', [EntityControllers\BookExportController::class, 'html']);
    Route::get('/books/{bookSlug}/export/pdf', [EntityControllers\BookExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/export/markdown', [EntityControllers\BookExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/export/zip', [EntityControllers\BookExportController::class, 'zip']);
    Route::get('/books/{bookSlug}/export/plaintext', [EntityControllers\BookExportController::class, 'plainText']);

    // Pages
    Route::get('/books/{bookSlug}/create-page', [EntityControllers\PageController::class, 'create']);
    Route::post('/books/{bookSlug}/create-guest-page', [EntityControllers\PageController::class, 'createAsGuest']);
    Route::get('/books/{bookSlug}/draft/{pageId}', [EntityControllers\PageController::class, 'editDraft']);
    Route::post('/books/{bookSlug}/draft/{pageId}', [EntityControllers\PageController::class, 'store']);
    Route::get('/books/{bookSlug}/page/{pageSlug}', [EntityControllers\PageController::class, 'show']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/pdf', [EntityControllers\PageExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/html', [EntityControllers\PageExportController::class, 'html']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/markdown', [EntityControllers\PageExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/export/plaintext', [EntityControllers\PageExportController::class, 'plainText']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/edit', [EntityControllers\PageController::class, 'edit']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/move', [EntityControllers\PageController::class, 'showMove']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/move', [EntityControllers\PageController::class, 'move']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/copy', [EntityControllers\PageController::class, 'showCopy']);
    Route::post('/books/{bookSlug}/page/{pageSlug}/copy', [EntityControllers\PageController::class, 'copy']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/delete', [EntityControllers\PageController::class, 'showDelete']);
    Route::get('/books/{bookSlug}/draft/{pageId}/delete', [EntityControllers\PageController::class, 'showDeleteDraft']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/permissions', [PermissionsController::class, 'showForPage']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/permissions', [PermissionsController::class, 'updateForPage']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/references', [ReferenceController::class, 'page']);
    Route::put('/books/{bookSlug}/page/{pageSlug}', [EntityControllers\PageController::class, 'update']);
    Route::delete('/books/{bookSlug}/page/{pageSlug}', [EntityControllers\PageController::class, 'destroy']);
    Route::delete('/books/{bookSlug}/draft/{pageId}', [EntityControllers\PageController::class, 'destroyDraft']);

    // Revisions
    Route::get('/books/{bookSlug}/page/{pageSlug}/revisions', [EntityControllers\PageRevisionController::class, 'index']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}', [EntityControllers\PageRevisionController::class, 'show']);
    Route::get('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}/changes', [EntityControllers\PageRevisionController::class, 'changes']);
    Route::put('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', [EntityControllers\PageRevisionController::class, 'restore']);
    Route::delete('/books/{bookSlug}/page/{pageSlug}/revisions/{revId}/delete', [EntityControllers\PageRevisionController::class, 'destroy']);
    Route::delete('/page-revisions/user-drafts/{pageId}', [EntityControllers\PageRevisionController::class, 'destroyUserDraft']);

    // Chapters
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/create-page', [EntityControllers\PageController::class, 'create']);
    Route::post('/books/{bookSlug}/chapter/{chapterSlug}/create-guest-page', [EntityControllers\PageController::class, 'createAsGuest']);
    Route::get('/books/{bookSlug}/create-chapter', [EntityControllers\ChapterController::class, 'create']);
    Route::post('/books/{bookSlug}/create-chapter', [EntityControllers\ChapterController::class, 'store']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}', [EntityControllers\ChapterController::class, 'show']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}', [EntityControllers\ChapterController::class, 'update']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/move', [EntityControllers\ChapterController::class, 'showMove']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}/move', [EntityControllers\ChapterController::class, 'move']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/copy', [EntityControllers\ChapterController::class, 'showCopy']);
    Route::post('/books/{bookSlug}/chapter/{chapterSlug}/copy', [EntityControllers\ChapterController::class, 'copy']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/edit', [EntityControllers\ChapterController::class, 'edit']);
    Route::post('/books/{bookSlug}/chapter/{chapterSlug}/convert-to-book', [EntityControllers\ChapterController::class, 'convertToBook']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/permissions', [PermissionsController::class, 'showForChapter']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/pdf', [EntityControllers\ChapterExportController::class, 'pdf']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/html', [EntityControllers\ChapterExportController::class, 'html']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/markdown', [EntityControllers\ChapterExportController::class, 'markdown']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/export/plaintext', [EntityControllers\ChapterExportController::class, 'plainText']);
    Route::put('/books/{bookSlug}/chapter/{chapterSlug}/permissions', [PermissionsController::class, 'updateForChapter']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/references', [ReferenceController::class, 'chapter']);
    Route::get('/books/{bookSlug}/chapter/{chapterSlug}/delete', [EntityControllers\ChapterController::class, 'showDelete']);
    Route::delete('/books/{bookSlug}/chapter/{chapterSlug}', [EntityControllers\ChapterController::class, 'destroy']);

    // User Profile routes
    Route::get('/user/{slug}', [UserControllers\UserProfileController::class, 'show']);

    // Image routes
    Route::get('/images/gallery', [UploadControllers\GalleryImageController::class, 'list']);
    Route::post('/images/gallery', [UploadControllers\GalleryImageController::class, 'create']);
    Route::get('/images/drawio', [UploadControllers\DrawioImageController::class, 'list']);
    Route::get('/images/drawio/base64/{id}', [UploadControllers\DrawioImageController::class, 'getAsBase64']);
    Route::post('/images/drawio', [UploadControllers\DrawioImageController::class, 'create']);
    Route::get('/images/edit/{id}', [UploadControllers\ImageController::class, 'edit']);
    Route::put('/images/{id}/file', [UploadControllers\ImageController::class, 'updateFile']);
    Route::put('/images/{id}/rebuild-thumbnails', [UploadControllers\ImageController::class, 'rebuildThumbnails']);
    Route::put('/images/{id}', [UploadControllers\ImageController::class, 'update']);
    Route::delete('/images/{id}', [UploadControllers\ImageController::class, 'destroy']);

    // Attachments routes
    Route::get('/attachments/{id}', [UploadControllers\AttachmentController::class, 'get']);
    Route::post('/attachments/upload', [UploadControllers\AttachmentController::class, 'upload']);
    Route::post('/attachments/upload/{id}', [UploadControllers\AttachmentController::class, 'uploadUpdate']);
    Route::post('/attachments/link', [UploadControllers\AttachmentController::class, 'attachLink']);
    Route::put('/attachments/{id}', [UploadControllers\AttachmentController::class, 'update']);
    Route::get('/attachments/edit/{id}', [UploadControllers\AttachmentController::class, 'getUpdateForm']);
    Route::get('/attachments/get/page/{pageId}', [UploadControllers\AttachmentController::class, 'listForPage']);
    Route::put('/attachments/sort/page/{pageId}', [UploadControllers\AttachmentController::class, 'sortForPage']);
    Route::delete('/attachments/{id}', [UploadControllers\AttachmentController::class, 'delete']);

    // AJAX routes
    Route::put('/ajax/page/{id}/save-draft', [EntityControllers\PageController::class, 'saveDraft']);
    Route::get('/ajax/page/{id}', [EntityControllers\PageController::class, 'getPageAjax']);
    Route::delete('/ajax/page/{id}', [EntityControllers\PageController::class, 'ajaxDestroy']);

    // Tag routes
    Route::get('/tags', [ActivityControllers\TagController::class, 'index']);
    Route::get('/ajax/tags/suggest/names', [ActivityControllers\TagController::class, 'getNameSuggestions']);
    Route::get('/ajax/tags/suggest/values', [ActivityControllers\TagController::class, 'getValueSuggestions']);

    // Comments
    Route::post('/comment/{pageId}', [ActivityControllers\CommentController::class, 'savePageComment']);
    Route::put('/comment/{id}', [ActivityControllers\CommentController::class, 'update']);
    Route::delete('/comment/{id}', [ActivityControllers\CommentController::class, 'destroy']);

    // Links
    Route::get('/link/{id}', [EntityControllers\PageController::class, 'redirectFromLink']);

    // Search
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/book/{bookId}', [SearchController::class, 'searchBook']);
    Route::get('/search/chapter/{bookId}', [SearchController::class, 'searchChapter']);
    Route::get('/search/entity/siblings', [SearchController::class, 'searchSiblings']);
    Route::get('/search/entity-selector', [SearchController::class, 'searchForSelector']);
    Route::get('/search/suggest', [SearchController::class, 'searchSuggestions']);

    // User Search
    Route::get('/search/users/select', [UserControllers\UserSearchController::class, 'forSelect']);

    // Template System
    Route::get('/templates', [EntityControllers\PageTemplateController::class, 'list']);
    Route::get('/templates/{templateId}', [EntityControllers\PageTemplateController::class, 'get']);

    // Favourites
    Route::get('/favourites', [ActivityControllers\FavouriteController::class, 'index']);
    Route::post('/favourites/add', [ActivityControllers\FavouriteController::class, 'add']);
    Route::post('/favourites/remove', [ActivityControllers\FavouriteController::class, 'remove']);

    // Watching
    Route::put('/watching/update', [ActivityControllers\WatchController::class, 'update']);

    // Other Pages
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index']);

    // Permissions
    Route::get('/permissions/form-row/{entityType}/{roleId}', [PermissionsController::class, 'formRowForRole']);

    // Maintenance
    Route::get('/settings/maintenance', [SettingControllers\MaintenanceController::class, 'index']);
    Route::delete('/settings/maintenance/cleanup-images', [SettingControllers\MaintenanceController::class, 'cleanupImages']);
    Route::post('/settings/maintenance/send-test-email', [SettingControllers\MaintenanceController::class, 'sendTestEmail']);
    Route::post('/settings/maintenance/regenerate-references', [SettingControllers\MaintenanceController::class, 'regenerateReferences']);

    // Recycle Bin
    Route::get('/settings/recycle-bin', [EntityControllers\RecycleBinController::class, 'index']);
    Route::post('/settings/recycle-bin/empty', [EntityControllers\RecycleBinController::class, 'empty']);
    Route::get('/settings/recycle-bin/{id}/destroy', [EntityControllers\RecycleBinController::class, 'showDestroy']);
    Route::delete('/settings/recycle-bin/{id}', [EntityControllers\RecycleBinController::class, 'destroy']);
    Route::get('/settings/recycle-bin/{id}/restore', [EntityControllers\RecycleBinController::class, 'showRestore']);
    Route::post('/settings/recycle-bin/{id}/restore', [EntityControllers\RecycleBinController::class, 'restore']);

    // Audit Log
    Route::get('/settings/audit', [ActivityControllers\AuditLogController::class, 'index']);

    // Users
    Route::get('/settings/users', [UserControllers\UserController::class, 'index']);
    Route::get('/settings/users/create', [UserControllers\UserController::class, 'create']);
    Route::get('/settings/users/{id}/delete', [UserControllers\UserController::class, 'delete']);
    Route::post('/settings/users/create', [UserControllers\UserController::class, 'store']);
    Route::get('/settings/users/{id}', [UserControllers\UserController::class, 'edit']);
    Route::put('/settings/users/{id}', [UserControllers\UserController::class, 'update']);
    Route::delete('/settings/users/{id}', [UserControllers\UserController::class, 'destroy']);

    // User Account
    Route::get('/my-account', [UserControllers\UserAccountController::class, 'redirect']);
    Route::get('/my-account/profile', [UserControllers\UserAccountController::class, 'showProfile']);
    Route::put('/my-account/profile', [UserControllers\UserAccountController::class, 'updateProfile']);
    Route::get('/my-account/shortcuts', [UserControllers\UserAccountController::class, 'showShortcuts']);
    Route::put('/my-account/shortcuts', [UserControllers\UserAccountController::class, 'updateShortcuts']);
    Route::get('/my-account/notifications', [UserControllers\UserAccountController::class, 'showNotifications']);
    Route::put('/my-account/notifications', [UserControllers\UserAccountController::class, 'updateNotifications']);
    Route::get('/my-account/auth', [UserControllers\UserAccountController::class, 'showAuth']);
    Route::put('/my-account/auth/password', [UserControllers\UserAccountController::class, 'updatePassword']);
    Route::get('/my-account/delete', [UserControllers\UserAccountController::class, 'delete']);
    Route::delete('/my-account', [UserControllers\UserAccountController::class, 'destroy']);

    // User Preference Endpoints
    Route::patch('/preferences/change-view/{type}', [UserControllers\UserPreferencesController::class, 'changeView']);
    Route::patch('/preferences/change-sort/{type}', [UserControllers\UserPreferencesController::class, 'changeSort']);
    Route::patch('/preferences/change-expansion/{type}', [UserControllers\UserPreferencesController::class, 'changeExpansion']);
    Route::patch('/preferences/toggle-dark-mode', [UserControllers\UserPreferencesController::class, 'toggleDarkMode']);
    Route::patch('/preferences/update-code-language-favourite', [UserControllers\UserPreferencesController::class, 'updateCodeLanguageFavourite']);

    // User API Tokens
    Route::get('/api-tokens/{userId}/create', [UserApiTokenController::class, 'create']);
    Route::post('/api-tokens/{userId}/create', [UserApiTokenController::class, 'store']);
    Route::get('/api-tokens/{userId}/{tokenId}', [UserApiTokenController::class, 'edit']);
    Route::put('/api-tokens/{userId}/{tokenId}', [UserApiTokenController::class, 'update']);
    Route::get('/api-tokens/{userId}/{tokenId}/delete', [UserApiTokenController::class, 'delete']);
    Route::delete('/api-tokens/{userId}/{tokenId}', [UserApiTokenController::class, 'destroy']);

    // Roles
    Route::get('/settings/roles', [UserControllers\RoleController::class, 'index']);
    Route::get('/settings/roles/new', [UserControllers\RoleController::class, 'create']);
    Route::post('/settings/roles/new', [UserControllers\RoleController::class, 'store']);
    Route::get('/settings/roles/delete/{id}', [UserControllers\RoleController::class, 'showDelete']);
    Route::delete('/settings/roles/delete/{id}', [UserControllers\RoleController::class, 'delete']);
    Route::get('/settings/roles/{id}', [UserControllers\RoleController::class, 'edit']);
    Route::put('/settings/roles/{id}', [UserControllers\RoleController::class, 'update']);

    // Webhooks
    Route::get('/settings/webhooks', [ActivityControllers\WebhookController::class, 'index']);
    Route::get('/settings/webhooks/create', [ActivityControllers\WebhookController::class, 'create']);
    Route::post('/settings/webhooks/create', [ActivityControllers\WebhookController::class, 'store']);
    Route::get('/settings/webhooks/{id}', [ActivityControllers\WebhookController::class, 'edit']);
    Route::put('/settings/webhooks/{id}', [ActivityControllers\WebhookController::class, 'update']);
    Route::get('/settings/webhooks/{id}/delete', [ActivityControllers\WebhookController::class, 'delete']);
    Route::delete('/settings/webhooks/{id}', [ActivityControllers\WebhookController::class, 'destroy']);

    // Settings
    Route::get('/settings', [SettingControllers\SettingController::class, 'index'])->name('settings');
    Route::get('/settings/{category}', [SettingControllers\SettingController::class, 'category'])->name('settings.category');
    Route::post('/settings/{category}', [SettingControllers\SettingController::class, 'update']);
});

// MFA routes
Route::middleware('mfa-setup')->group(function () {
    Route::get('/mfa/setup', [AccessControllers\MfaController::class, 'setup']);
    Route::get('/mfa/totp/generate', [AccessControllers\MfaTotpController::class, 'generate']);
    Route::post('/mfa/totp/confirm', [AccessControllers\MfaTotpController::class, 'confirm']);
    Route::get('/mfa/backup_codes/generate', [AccessControllers\MfaBackupCodesController::class, 'generate']);
    Route::post('/mfa/backup_codes/confirm', [AccessControllers\MfaBackupCodesController::class, 'confirm']);
});
Route::middleware('guest')->group(function () {
    Route::get('/mfa/verify', [AccessControllers\MfaController::class, 'verify']);
    Route::post('/mfa/totp/verify', [AccessControllers\MfaTotpController::class, 'verify']);
    Route::post('/mfa/backup_codes/verify', [AccessControllers\MfaBackupCodesController::class, 'verify']);
});
Route::delete('/mfa/{method}/remove', [AccessControllers\MfaController::class, 'remove'])->middleware('auth');

// Social auth routes
Route::get('/login/service/{socialDriver}', [AccessControllers\SocialController::class, 'login']);
Route::get('/login/service/{socialDriver}/callback', [AccessControllers\SocialController::class, 'callback']);
Route::post('/login/service/{socialDriver}/detach', [AccessControllers\SocialController::class, 'detach'])->middleware('auth');
Route::get('/register/service/{socialDriver}', [AccessControllers\SocialController::class, 'register']);

// Login/Logout routes
Route::get('/login', [AccessControllers\LoginController::class, 'getLogin']);
Route::post('/login', [AccessControllers\LoginController::class, 'login']);
Route::post('/logout', [AccessControllers\LoginController::class, 'logout']);
Route::get('/register', [AccessControllers\RegisterController::class, 'getRegister']);
Route::get('/register/confirm', [AccessControllers\ConfirmEmailController::class, 'show']);
Route::get('/register/confirm/awaiting', [AccessControllers\ConfirmEmailController::class, 'showAwaiting']);
Route::post('/register/confirm/resend', [AccessControllers\ConfirmEmailController::class, 'resend']);
Route::get('/register/confirm/{token}', [AccessControllers\ConfirmEmailController::class, 'showAcceptForm']);
Route::post('/register/confirm/accept', [AccessControllers\ConfirmEmailController::class, 'confirm']);
Route::post('/register', [AccessControllers\RegisterController::class, 'postRegister']);

// SAML routes
Route::post('/saml2/login', [AccessControllers\Saml2Controller::class, 'login']);
Route::post('/saml2/logout', [AccessControllers\Saml2Controller::class, 'logout']);
Route::get('/saml2/metadata', [AccessControllers\Saml2Controller::class, 'metadata']);
Route::get('/saml2/sls', [AccessControllers\Saml2Controller::class, 'sls']);
Route::post('/saml2/acs', [AccessControllers\Saml2Controller::class, 'startAcs'])->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
]);
Route::get('/saml2/acs', [AccessControllers\Saml2Controller::class, 'processAcs']);

// OIDC routes
Route::post('/oidc/login', [AccessControllers\OidcController::class, 'login']);
Route::get('/oidc/callback', [AccessControllers\OidcController::class, 'callback']);
Route::post('/oidc/logout', [AccessControllers\OidcController::class, 'logout']);

// User invitation routes
Route::get('/register/invite/{token}', [AccessControllers\UserInviteController::class, 'showSetPassword']);
Route::post('/register/invite/{token}', [AccessControllers\UserInviteController::class, 'setPassword']);

// Password reset link request routes
Route::get('/password/email', [AccessControllers\ForgotPasswordController::class, 'showLinkRequestForm']);
Route::post('/password/email', [AccessControllers\ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password reset routes
Route::get('/password/reset/{token}', [AccessControllers\ResetPasswordController::class, 'showResetForm']);
Route::post('/password/reset', [AccessControllers\ResetPasswordController::class, 'reset']);

// Metadata routes
Route::view('/help/wysiwyg', 'help.wysiwyg');

Route::fallback([HomeController::class, 'notFound'])->name('fallback');
