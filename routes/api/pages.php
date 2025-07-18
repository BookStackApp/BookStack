<?php

use BookStack\Entities\Controllers as EntityControllers;
use BookStack\Exports\Controllers as ExportControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [EntityControllers\PageApiController::class, 'list']);
Route::post('/', [EntityControllers\PageApiController::class, 'create']);
Route::get('/{id}', [EntityControllers\PageApiController::class, 'read']);
Route::put('/{id}', [EntityControllers\PageApiController::class, 'update']);
Route::delete('/{id}', [EntityControllers\PageApiController::class, 'delete']);

Route::get('/{id}/export/html', [ExportControllers\PageExportApiController::class, 'exportHtml']);
Route::get('/{id}/export/pdf', [ExportControllers\PageExportApiController::class, 'exportPdf']);
Route::get('/{id}/export/plaintext', [ExportControllers\PageExportApiController::class, 'exportPlainText']);
Route::get('/{id}/export/markdown', [ExportControllers\PageExportApiController::class, 'exportMarkdown']);