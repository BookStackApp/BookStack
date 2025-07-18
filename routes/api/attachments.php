<?php

use BookStack\Uploads\Controllers\AttachmentApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttachmentApiController::class, 'list']);
Route::post('/', [AttachmentApiController::class, 'create']);
Route::get('/{id}', [AttachmentApiController::class, 'read']);
Route::put('/{id}', [AttachmentApiController::class, 'update']);
Route::delete('/{id}', [AttachmentApiController::class, 'delete']);
