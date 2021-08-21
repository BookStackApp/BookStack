<?php

Route::get('/status', 'StatusController@show');
Route::get('/robots.txt', 'HomeController@getRobots');

// Authenticated routes...
Route::group(['middleware' => 'auth'], function () {

    // Secure images routing
    Route::get('/uploads/images/{path}', 'Images\ImageController@showImage')
        ->where('path', '.*$');

    Route::get('/pages/recently-updated', 'PageController@showRecentlyUpdated');

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
    });

    // User Profile routes
    Route::get('/user/{slug}', 'UserProfileController@show');

    // Image routes
    Route::get('/images/gallery', 'Images\GalleryImageController@list');
    Route::post('/images/gallery', 'Images\GalleryImageController@create');
    Route::get('/images/drawio', 'Images\DrawioImageController@list');
    Route::get('/images/drawio/base64/{id}', 'Images\DrawioImageController@getAsBase64');
    Route::post('/images/drawio', 'Images\DrawioImageController@create');
    Route::get('/images/edit/{id}', 'Images\ImageController@edit');
    Route::put('/images/{id}', 'Images\ImageController@update');
    Route::delete('/images/{id}', 'Images\ImageController@destroy');

    // Attachments routes
    Route::get('/attachments/{id}', 'AttachmentController@get');
    Route::post('/attachments/upload', 'AttachmentController@upload');
    Route::post('/attachments/upload/{id}', 'AttachmentController@uploadUpdate');
    Route::post('/attachments/link', 'AttachmentController@attachLink');
    Route::put('/attachments/{id}', 'AttachmentController@update');
    Route::get('/attachments/edit/{id}', 'AttachmentController@getUpdateForm');
    Route::get('/attachments/get/page/{pageId}', 'AttachmentController@listForPage');
    Route::put('/attachments/sort/page/{pageId}', 'AttachmentController@sortForPage');
    Route::delete('/attachments/{id}', 'AttachmentController@delete');

    // AJAX routes
    Route::put('/ajax/page/{id}/save-draft', 'PageController@saveDraft');
    Route::get('/ajax/page/{id}', 'PageController@getPageAjax');
    Route::delete('/ajax/page/{id}', 'PageController@ajaxDestroy');

    // Tag routes (AJAX)
    Route::group(['prefix' => 'ajax/tags'], function () {
        Route::get('/suggest/names', 'TagController@getNameSuggestions');
        Route::get('/suggest/values', 'TagController@getValueSuggestions');
    });

    Route::get('/ajax/search/entities', 'SearchController@searchEntitiesAjax');

    // Comments
    Route::post('/comment/{pageId}', 'CommentController@savePageComment');
    Route::put('/comment/{id}', 'CommentController@update');
    Route::delete('/comment/{id}', 'CommentController@destroy');

    // Links
    Route::get('/link/{id}', 'PageController@redirectFromLink');

    // Search
    Route::get('/search', 'SearchController@search');
    Route::get('/search/book/{bookId}', 'SearchController@searchBook');
    Route::get('/search/chapter/{bookId}', 'SearchController@searchChapter');
    Route::get('/search/entity/siblings', 'SearchController@searchSiblings');

    // User Search
    Route::get('/search/users/select', 'UserSearchController@forSelect');

    // Template System
    Route::get('/templates', 'PageTemplateController@list');
    Route::get('/templates/{templateId}', 'PageTemplateController@get');

    // Favourites
    Route::get('/favourites', 'FavouriteController@index');
    Route::post('/favourites/add', 'FavouriteController@add');
    Route::post('/favourites/remove', 'FavouriteController@remove');

    // Other Pages
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('/custom-head-content', 'HomeController@customHeadContent');

    // Settings
    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'SettingController@index')->name('settings');
        Route::post('/', 'SettingController@update');

        // Maintenance
        Route::get('/maintenance', 'MaintenanceController@index');
        Route::delete('/maintenance/cleanup-images', 'MaintenanceController@cleanupImages');
        Route::post('/maintenance/send-test-email', 'MaintenanceController@sendTestEmail');

        // Recycle Bin
        Route::get('/recycle-bin', 'RecycleBinController@index');
        Route::post('/recycle-bin/empty', 'RecycleBinController@empty');
        Route::get('/recycle-bin/{id}/destroy', 'RecycleBinController@showDestroy');
        Route::delete('/recycle-bin/{id}', 'RecycleBinController@destroy');
        Route::get('/recycle-bin/{id}/restore', 'RecycleBinController@showRestore');
        Route::post('/recycle-bin/{id}/restore', 'RecycleBinController@restore');

        // Audit Log
        Route::get('/audit', 'AuditLogController@index');

        // Users
        Route::get('/users', 'UserController@index');
        Route::get('/users/create', 'UserController@create');
        Route::get('/users/{id}/delete', 'UserController@delete');
        Route::patch('/users/{id}/switch-books-view', 'UserController@switchBooksView');
        Route::patch('/users/{id}/switch-shelves-view', 'UserController@switchShelvesView');
        Route::patch('/users/{id}/switch-shelf-view', 'UserController@switchShelfView');
        Route::patch('/users/{id}/change-sort/{type}', 'UserController@changeSort');
        Route::patch('/users/{id}/update-expansion-preference/{key}', 'UserController@updateExpansionPreference');
        Route::patch('/users/toggle-dark-mode', 'UserController@toggleDarkMode');
        Route::post('/users/create', 'UserController@store');
        Route::get('/users/{id}', 'UserController@edit');
        Route::put('/users/{id}', 'UserController@update');
        Route::delete('/users/{id}', 'UserController@destroy');

        // User API Tokens
        Route::get('/users/{userId}/create-api-token', 'UserApiTokenController@create');
        Route::post('/users/{userId}/create-api-token', 'UserApiTokenController@store');
        Route::get('/users/{userId}/api-tokens/{tokenId}', 'UserApiTokenController@edit');
        Route::put('/users/{userId}/api-tokens/{tokenId}', 'UserApiTokenController@update');
        Route::get('/users/{userId}/api-tokens/{tokenId}/delete', 'UserApiTokenController@delete');
        Route::delete('/users/{userId}/api-tokens/{tokenId}', 'UserApiTokenController@destroy');

        // Roles
        Route::get('/roles', 'RoleController@list');
        Route::get('/roles/new', 'RoleController@create');
        Route::post('/roles/new', 'RoleController@store');
        Route::get('/roles/delete/{id}', 'RoleController@showDelete');
        Route::delete('/roles/delete/{id}', 'RoleController@delete');
        Route::get('/roles/{id}', 'RoleController@edit');
        Route::put('/roles/{id}', 'RoleController@update');
    });

});

// MFA routes
Route::group(['middleware' => 'mfa-setup'], function() {
    Route::get('/mfa/setup', 'Auth\MfaController@setup');
    Route::get('/mfa/totp/generate', 'Auth\MfaTotpController@generate');
    Route::post('/mfa/totp/confirm', 'Auth\MfaTotpController@confirm');
    Route::get('/mfa/backup_codes/generate', 'Auth\MfaBackupCodesController@generate');
    Route::post('/mfa/backup_codes/confirm', 'Auth\MfaBackupCodesController@confirm');
});
Route::group(['middleware' => 'guest'], function() {
    Route::get('/mfa/verify', 'Auth\MfaController@verify');
    Route::post('/mfa/totp/verify', 'Auth\MfaTotpController@verify');
    Route::post('/mfa/backup_codes/verify', 'Auth\MfaBackupCodesController@verify');
});
Route::delete('/mfa/{method}/remove', 'Auth\MfaController@remove')->middleware('auth');

// Social auth routes
Route::get('/login/service/{socialDriver}', 'Auth\SocialController@login');
Route::get('/login/service/{socialDriver}/callback', 'Auth\SocialController@callback');
Route::post('/login/service/{socialDriver}/detach', 'Auth\SocialController@detach')->middleware('auth');
Route::get('/register/service/{socialDriver}', 'Auth\SocialController@register');

// Login/Logout routes
Route::get('/login', 'Auth\LoginController@getLogin');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/register', 'Auth\RegisterController@getRegister');
Route::get('/register/confirm', 'Auth\ConfirmEmailController@show');
Route::get('/register/confirm/awaiting', 'Auth\ConfirmEmailController@showAwaiting');
Route::post('/register/confirm/resend', 'Auth\ConfirmEmailController@resend');
Route::get('/register/confirm/{token}', 'Auth\ConfirmEmailController@confirm');
Route::post('/register', 'Auth\RegisterController@postRegister');

// SAML routes
Route::post('/saml2/login', 'Auth\Saml2Controller@login');
Route::get('/saml2/logout', 'Auth\Saml2Controller@logout');
Route::get('/saml2/metadata', 'Auth\Saml2Controller@metadata');
Route::get('/saml2/sls', 'Auth\Saml2Controller@sls');
Route::post('/saml2/acs', 'Auth\Saml2Controller@acs');

// User invitation routes
Route::get('/register/invite/{token}', 'Auth\UserInviteController@showSetPassword');
Route::post('/register/invite/{token}', 'Auth\UserInviteController@setPassword');

// Password reset link request routes...
Route::get('/password/email', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

// Password reset routes...
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');

Route::fallback('HomeController@getNotFound')->name('fallback');
