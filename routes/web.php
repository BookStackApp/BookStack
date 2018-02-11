<?php

Route::get('/translations', 'HomeController@getTranslations');

// Authenticated routes...
Route::group(['middleware' => 'auth'], function () {

    Route::get('/uploads/images/{path}', 'ImageController@showImage')
        ->where('path', '.*$');

    Route::group(['prefix' => 'pages'], function() {
        Route::get('/recently-created', 'PageController@showRecentlyCreated');
        Route::get('/recently-updated', 'PageController@showRecentlyUpdated');
    });

    Route::group(['prefix' => 'books'], function () {

        // Books
        Route::get('/', 'BookController@index');
        Route::get('/create', 'BookController@create');
        Route::post('/', 'BookController@store');
        Route::get('/{slug}/edit', 'BookController@edit');
        Route::put('/{slug}', 'BookController@update');
        Route::delete('/{id}', 'BookController@destroy');
        Route::get('/{slug}/sort-item', 'BookController@getSortItem');
        Route::get('/{slug}', 'BookController@show');
        Route::get('/{bookSlug}/permissions', 'BookController@showRestrict');
        Route::put('/{bookSlug}/permissions', 'BookController@restrict');
        Route::get('/{slug}/delete', 'BookController@showDelete');
        Route::get('/{bookSlug}/sort', 'BookController@sort');
        Route::put('/{bookSlug}/sort', 'BookController@saveSort');
        Route::get('/{bookSlug}/export/html', 'BookController@exportHtml');
        Route::get('/{bookSlug}/export/pdf', 'BookController@exportPdf');
        Route::get('/{bookSlug}/export/plaintext', 'BookController@exportPlainText');

        // Pages
        Route::get('/{bookSlug}/page/create', 'PageController@create');
        Route::post('/{bookSlug}/page/create/guest', 'PageController@createAsGuest');
        Route::get('/{bookSlug}/draft/{pageId}', 'PageController@editDraft');
        Route::post('/{bookSlug}/draft/{pageId}', 'PageController@store');
        Route::get('/{bookSlug}/page/{pageSlug}', 'PageController@show');
        Route::get('/{bookSlug}/page/{pageSlug}/export/pdf', 'PageController@exportPdf');
        Route::get('/{bookSlug}/page/{pageSlug}/export/html', 'PageController@exportHtml');
        Route::get('/{bookSlug}/page/{pageSlug}/export/plaintext', 'PageController@exportPlainText');
        Route::get('/{bookSlug}/page/{pageSlug}/edit', 'PageController@edit');
        Route::get('/{bookSlug}/page/{pageSlug}/move', 'PageController@showMove');
        Route::put('/{bookSlug}/page/{pageSlug}/move', 'PageController@move');
        Route::get('/{bookSlug}/page/{pageSlug}/delete', 'PageController@showDelete');
        Route::get('/{bookSlug}/draft/{pageId}/delete', 'PageController@showDeleteDraft');
        Route::get('/{bookSlug}/page/{pageSlug}/permissions', 'PageController@showRestrict');
        Route::put('/{bookSlug}/page/{pageSlug}/permissions', 'PageController@restrict');
        Route::put('/{bookSlug}/page/{pageSlug}', 'PageController@update');
        Route::delete('/{bookSlug}/page/{pageSlug}', 'PageController@destroy');
        Route::delete('/{bookSlug}/draft/{pageId}', 'PageController@destroyDraft');

        // Revisions
        Route::get('/{bookSlug}/page/{pageSlug}/revisions', 'PageController@showRevisions');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}', 'PageController@showRevision');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/changes', 'PageController@showRevisionChanges');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', 'PageController@restoreRevision');

        // Chapters
        Route::get('/{bookSlug}/chapter/{chapterSlug}/create-page', 'PageController@create');
        Route::post('/{bookSlug}/chapter/{chapterSlug}/page/create/guest', 'PageController@createAsGuest');
        Route::get('/{bookSlug}/chapter/create', 'ChapterController@create');
        Route::post('/{bookSlug}/chapter/create', 'ChapterController@store');
        Route::get('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@show');
        Route::put('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@update');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/move', 'ChapterController@showMove');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/move', 'ChapterController@move');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/edit', 'ChapterController@edit');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@showRestrict');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/pdf', 'ChapterController@exportPdf');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/html', 'ChapterController@exportHtml');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/plaintext', 'ChapterController@exportPlainText');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@restrict');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/delete', 'ChapterController@showDelete');
        Route::delete('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@destroy');

    });

    // User Profile routes
    Route::get('/user/{userId}', 'UserController@showProfilePage');

    // Image routes
    Route::group(['prefix' => 'images'], function() {
        // Get for user images
        Route::get('/user/all', 'ImageController@getAllForUserType');
        Route::get('/user/all/{page}', 'ImageController@getAllForUserType');
        // Standard get, update and deletion for all types
        Route::get('/thumb/{id}/{width}/{height}/{crop}', 'ImageController@getThumbnail');
        Route::get('/base64/{id}', 'ImageController@getBase64Image');
        Route::put('/update/{imageId}', 'ImageController@update');
        Route::post('/drawing/upload', 'ImageController@uploadDrawing');
        Route::put('/drawing/upload/{id}', 'ImageController@replaceDrawing');
        Route::post('/{type}/upload', 'ImageController@uploadByType');
        Route::get('/{type}/all', 'ImageController@getAllByType');
        Route::get('/{type}/all/{page}', 'ImageController@getAllByType');
        Route::get('/{type}/search/{page}', 'ImageController@searchByType');
        Route::get('/gallery/{filter}/{page}', 'ImageController@getGalleryFiltered');
        Route::delete('/{id}', 'ImageController@destroy');
    });

    // Attachments routes
    Route::get('/attachments/{id}', 'AttachmentController@get');
    Route::post('/attachments/upload', 'AttachmentController@upload');
    Route::post('/attachments/upload/{id}', 'AttachmentController@uploadUpdate');
    Route::post('/attachments/link', 'AttachmentController@attachLink');
    Route::put('/attachments/{id}', 'AttachmentController@update');
    Route::get('/attachments/get/page/{pageId}', 'AttachmentController@listForPage');
    Route::put('/attachments/sort/page/{pageId}', 'AttachmentController@sortForPage');
    Route::delete('/attachments/{id}', 'AttachmentController@delete');

    // AJAX routes
    Route::put('/ajax/page/{id}/save-draft', 'PageController@saveDraft');
    Route::get('/ajax/page/{id}', 'PageController@getPageAjax');
    Route::delete('/ajax/page/{id}', 'PageController@ajaxDestroy');

    // Tag routes (AJAX)
    Route::group(['prefix' => 'ajax/tags'], function() {
        Route::get('/get/{entityType}/{entityId}', 'TagController@getForEntity');
        Route::get('/suggest/names', 'TagController@getNameSuggestions');
        Route::get('/suggest/values', 'TagController@getValueSuggestions');
    });

    Route::get('/ajax/search/entities', 'SearchController@searchEntitiesAjax');

    // Comments
    Route::post('/ajax/page/{pageId}/comment', 'CommentController@savePageComment');
    Route::put('/ajax/comment/{id}', 'CommentController@update');
    Route::delete('/ajax/comment/{id}', 'CommentController@destroy');

    // Links
    Route::get('/link/{id}', 'PageController@redirectFromLink');

    // Search
    Route::get('/search', 'SearchController@search');
    Route::get('/search/book/{bookId}', 'SearchController@searchBook');
    Route::get('/search/chapter/{bookId}', 'SearchController@searchChapter');

    // Other Pages
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('/custom-head-content', 'HomeController@customHeadContent');

    // Settings
    Route::group(['prefix' => 'settings'], function() {
        Route::get('/', 'SettingController@index')->name('settings');
        Route::post('/', 'SettingController@update');

        // Users
        Route::get('/users', 'UserController@index');
        Route::get('/users/create', 'UserController@create');
        Route::get('/users/{id}/delete', 'UserController@delete');
        Route::patch('/users/{id}/switch-book-view', 'UserController@switchBookView');
        Route::post('/users/create', 'UserController@store');
        Route::get('/users/{id}', 'UserController@edit');
        Route::put('/users/{id}', 'UserController@update');
        Route::delete('/users/{id}', 'UserController@destroy');

        // Roles
        Route::get('/roles', 'PermissionController@listRoles');
        Route::get('/roles/new', 'PermissionController@createRole');
        Route::post('/roles/new', 'PermissionController@storeRole');
        Route::get('/roles/delete/{id}', 'PermissionController@showDeleteRole');
        Route::delete('/roles/delete/{id}', 'PermissionController@deleteRole');
        Route::get('/roles/{id}', 'PermissionController@editRole');
        Route::put('/roles/{id}', 'PermissionController@updateRole');
    });

});

// Social auth routes
Route::get('/login/service/{socialDriver}', 'Auth\LoginController@getSocialLogin');
Route::get('/login/service/{socialDriver}/callback', 'Auth\RegisterController@socialCallback');
Route::get('/login/service/{socialDriver}/detach', 'Auth\RegisterController@detachSocialAccount');
Route::get('/register/service/{socialDriver}', 'Auth\RegisterController@socialRegister');

// Login/Logout routes
Route::get('/login', 'Auth\LoginController@getLogin');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/register', 'Auth\RegisterController@getRegister');
Route::get('/register/confirm', 'Auth\RegisterController@getRegisterConfirmation');
Route::get('/register/confirm/awaiting', 'Auth\RegisterController@showAwaitingConfirmation');
Route::post('/register/confirm/resend', 'Auth\RegisterController@resendConfirmation');
Route::get('/register/confirm/{token}', 'Auth\RegisterController@confirmEmail');
Route::post('/register', 'Auth\RegisterController@postRegister');

// Password reset link request routes...
Route::get('/password/email', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

// Password reset routes...
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');