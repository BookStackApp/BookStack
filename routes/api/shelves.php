<?php

use Illuminate\Support\Facades\Route;
use BookStack\Entities\Controllers as EntityControllers;

Route::get('/', [EntityControllers\BookshelfApiController::class, 'list']);
Route::post('/', [EntityControllers\BookshelfApiController::class, 'create']);
Route::get('/{id}', [EntityControllers\BookshelfApiController::class, 'read']);
Route::put('/{id}', [EntityControllers\BookshelfApiController::class, 'update']);
Route::delete('/{id}', [EntityControllers\BookshelfApiController::class, 'delete']);