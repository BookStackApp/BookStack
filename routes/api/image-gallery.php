<?php

use BookStack\Uploads\Controllers\ImageGalleryApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ImageGalleryApiController::class, 'list']);
Route::post('/', [ImageGalleryApiController::class, 'create']);
Route::get('/{id}', [ImageGalleryApiController::class, 'read']);
Route::put('/{id}', [ImageGalleryApiController::class, 'update']);
Route::delete('/{id}', [ImageGalleryApiController::class, 'delete']);