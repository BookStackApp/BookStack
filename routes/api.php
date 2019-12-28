<?php

/**
 * Routes for the BookStack API.
 *
 * Routes have a uri prefix of /api/.
 */


// TODO - Authenticate middleware

Route::get('books', 'BooksApiController@index');