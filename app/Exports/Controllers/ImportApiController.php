<?php

declare(strict_types=1);

namespace BookStack\Exports\Controllers;

use BookStack\Exceptions\ZipImportException;
use BookStack\Exceptions\ZipValidationException;
use BookStack\Exports\ImportRepo;
use BookStack\Http\Controller;
use BookStack\Uploads\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImportApiController extends Controller
{
    public function __construct(
        protected ImportRepo $imports,
    ) {
        $this->middleware('can:content-import');
    }

    /**
     * List existing imports visible to the user.
     */
    public function list(): JsonResponse
    {
        $imports = $this->imports->getVisibleImports();

        return response()->json([
            'status' => 'success',
            'imports' => $imports,
        ]);
    }

    /**
     * Upload, validate and store an import file.
     */
    public function upload(Request $request): JsonResponse
    {
        $this->validate($request, [
            'file' => ['required', ...AttachmentService::getFileValidationRules()]
        ]);

        $file = $request->file('file');

        try {
            $import = $this->imports->storeFromUpload($file);
        } catch (ZipValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->errors,
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'import' => $import,
        ], 201);
    }

    /**
     * Show details of a pending import.
     */
    public function read(int $id): JsonResponse
    {
        $import = $this->imports->findVisible($id);

        return response()->json([
            'status' => 'success',
            'import' => $import,
            'data' => $import->decodeMetadata(),
        ]);
    }

    /**
     * Run the import process.
     */
    public function create(int $id, Request $request): JsonResponse
    {
        $import = $this->imports->findVisible($id);
        $parent = null;

        if ($import->type === 'page' || $import->type === 'chapter') {
            $data = $this->validate($request, [
                'parent' => ['required', 'string'],
            ]);
            $parent = $data['parent'];
        }

        try {
            $entity = $this->imports->runImport($import, $parent);
        } catch (ZipImportException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed',
                'errors' => $exception->errors,
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'entity' => $entity,
        ]);
    }

    /**
     * Delete a pending import.
     */
    public function delete(int $id): JsonResponse
    {
        $import = $this->imports->findVisible($id);
        $this->imports->deleteImport($import);

        return response()->json([
            'status' => 'success',
            'message' => 'Import deleted successfully',
        ]);
    }
}