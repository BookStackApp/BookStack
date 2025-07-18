<?php

use BookStack\Users\Controllers\RoleApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RoleApiController::class, 'list']);
Route::post('/', [RoleApiController::class, 'create']);
Route::get('/{id}', [RoleApiController::class, 'read']);
Route::put('/{id}', [RoleApiController::class, 'update']);
Route::delete('/{id}', [RoleApiController::class, 'delete']);