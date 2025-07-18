<?php

use BookStack\Entities\Controllers as EntityControllers;
use BookStack\Exports\Controllers as ExportControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [EntityControllers\ChapterApiController::class, 'list']);
Route::post('/', [EntityControllers\ChapterApiController::class, 'create']);
Route::get('/{id}', [EntityControllers\ChapterApiController::class, 'read']);
Route::put('/{id}', [EntityControllers\ChapterApiController::class, 'update']);
Route::delete('/{id}', [EntityControllers\ChapterApiController::class, 'delete']);
Route::get('/{id}/export/html', [ExportControllers\ChapterExportApiController::class, 'exportHtml']);
Route::get('/{id}/export/pdf', [ExportControllers\ChapterExportApiController::class, 'exportPdf']);
Route::get('/{id}/export/plaintext', [ExportControllers\ChapterExportApiController::class, 'exportPlainText']);
Route::get('/{id}/export/markdown', [ExportControllers\ChapterExportApiController::class, 'exportMarkdown']);