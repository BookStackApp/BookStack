<?php

use BookStack\Entities\Controllers as EntityControllers;
use BookStack\Exports\Controllers as ExportControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [EntityControllers\BookApiController::class, 'list']);
Route::post('/', [EntityControllers\BookApiController::class, 'create']);
Route::get('/{id}', [EntityControllers\BookApiController::class, 'read']);
Route::put('/{id}', [EntityControllers\BookApiController::class, 'update']);
Route::delete('/{id}', [EntityControllers\BookApiController::class, 'delete']);

Route::get('/{id}/export/html', [ExportControllers\BookExportApiController::class, 'exportHtml']);
Route::get('/{id}/export/pdf', [ExportControllers\BookExportApiController::class, 'exportPdf']);
Route::get('/{id}/export/plaintext', [ExportControllers\BookExportApiController::class, 'exportPlainText']);
Route::get('/{id}/export/markdown', [ExportControllers\BookExportApiController::class, 'exportMarkdown']);