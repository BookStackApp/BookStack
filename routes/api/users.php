<?php

use BookStack\Users\Controllers\UserApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserApiController::class, 'list']);
Route::post('/', [UserApiController::class, 'create']);
Route::get('/{id}', [UserApiController::class, 'read']);
Route::put('/{id}', [UserApiController::class, 'update']);
Route::delete('/{id}', [UserApiController::class, 'delete']);