<?php

// Authenticated routes...
Route::group(['middleware' => 'auth'], function () {

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

        // Pages
        Route::get('/{bookSlug}/page/create', 'PageController@create');
        Route::get('/{bookSlug}/draft/{pageId}', 'PageController@editDraft');
        Route::post('/{bookSlug}/draft/{pageId}', 'PageController@store');
        Route::get('/{bookSlug}/page/{pageSlug}', 'PageController@show');
        Route::get('/{bookSlug}/page/{pageSlug}/export/pdf', 'PageController@exportPdf');
        Route::get('/{bookSlug}/page/{pageSlug}/export/html', 'PageController@exportHtml');
        Route::get('/{bookSlug}/page/{pageSlug}/export/plaintext', 'PageController@exportPlainText');
        Route::get('/{bookSlug}/page/{pageSlug}/edit', 'PageController@edit');
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
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', 'PageController@restoreRevision');

        // Chapters
        Route::get('/{bookSlug}/chapter/{chapterSlug}/create-page', 'PageController@create');
        Route::get('/{bookSlug}/chapter/create', 'ChapterController@create');
        Route::post('/{bookSlug}/chapter/create', 'ChapterController@store');
        Route::get('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@show');
        Route::put('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@update');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/edit', 'ChapterController@edit');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@showRestrict');
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
        Route::put('/update/{imageId}', 'ImageController@update');
        Route::post('/{type}/upload', 'ImageController@uploadByType');
        Route::get('/{type}/all', 'ImageController@getAllByType');
        Route::get('/{type}/all/{page}', 'ImageController@getAllByType');
        Route::get('/{type}/search/{page}', 'ImageController@searchByType');
        Route::get('/gallery/{filter}/{page}', 'ImageController@getGalleryFiltered');
        Route::delete('/{imageId}', 'ImageController@destroy');
    });

    // AJAX routes
    Route::put('/ajax/page/{id}/save-draft', 'PageController@saveDraft');
    Route::get('/ajax/page/{id}', 'PageController@getPageAjax');
    Route::delete('/ajax/page/{id}', 'PageController@ajaxDestroy');

    // Tag routes (AJAX)
    Route::group(['prefix' => 'ajax/tags'], function() {
        Route::get('/get/{entityType}/{entityId}', 'TagController@getForEntity');
        Route::get('/suggest/names', 'TagController@getNameSuggestions');
        Route::get('/suggest/values', 'TagController@getValueSuggestions');
        Route::post('/update/{entityType}/{entityId}', 'TagController@updateForEntity');
    });

    // Links
    Route::get('/link/{id}', 'PageController@redirectFromLink');

    // Search
    Route::get('/search/all', 'SearchController@searchAll');
    Route::get('/search/pages', 'SearchController@searchPages');
    Route::get('/search/books', 'SearchController@searchBooks');
    Route::get('/search/chapters', 'SearchController@searchChapters');
    Route::get('/search/book/{bookId}', 'SearchController@searchBook');

    // Other Pages
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');

    // Settings
    Route::group(['prefix' => 'settings'], function() {
        Route::get('/', 'SettingController@index');
        Route::post('/', 'SettingController@update');

        // Users
        Route::get('/users', 'UserController@index');
        Route::get('/users/create', 'UserController@create');
        Route::get('/users/{id}/delete', 'UserController@delete');
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

// Login using social authentication
Route::get('/login/service/{socialDriver}', 'Auth\AuthController@getSocialLogin');
Route::get('/login/service/{socialDriver}/callback', 'Auth\AuthController@socialCallback');
Route::get('/login/service/{socialDriver}/detach', 'Auth\AuthController@detachSocialAccount');

// Login/Logout routes
Route::get('/login', 'Auth\AuthController@getLogin');
Route::post('/login', 'Auth\AuthController@postLogin');
Route::get('/logout', 'Auth\AuthController@getLogout');
Route::get('/register', 'Auth\AuthController@getRegister');
Route::get('/register/confirm', 'Auth\AuthController@getRegisterConfirmation');
Route::get('/register/confirm/awaiting', 'Auth\AuthController@showAwaitingConfirmation');
Route::post('/register/confirm/resend', 'Auth\AuthController@resendConfirmation');
Route::get('/register/confirm/{token}', 'Auth\AuthController@confirmEmail');
Route::get('/register/confirm/{token}/email', 'Auth\AuthController@viewConfirmEmail');
Route::get('/register/service/{socialDriver}', 'Auth\AuthController@socialRegister');
Route::post('/register', 'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('/password/email', 'Auth\PasswordController@getEmail');
Route::post('/password/email', 'Auth\PasswordController@postEmail');
// Password reset routes...
Route::get('/password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('/password/reset', 'Auth\PasswordController@postReset');