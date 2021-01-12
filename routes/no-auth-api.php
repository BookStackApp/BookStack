<?php

/**
 * Routes for the BookStack API which do not require authentication.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api
 */

Route::get('simple-status', 'StatusController@simpleStatus');
Route::get('status', 'StatusController@status');
