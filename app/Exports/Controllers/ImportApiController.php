<?php

declare(strict_types=1);

namespace BookStack\Exports\Controllers;

use BookStack\Exceptions\ZipImportException;
use BookStack\Exceptions\ZipValidationException;
use BookStack\Exports\ImportRepo;
use BookStack\Http\ApiController;
use BookStack\Uploads\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ImportApiController extends ApiController
{
    public function __construct(
        protected ImportRepo $imports,
    ) {
        $this->middleware('can:content-import');
    }

    /**
     * List existing ZIP imports visible to the user.
     */
    public function list(): JsonResponse
    {
        $imports = $this->imports->getVisibleImports()->all();

        return response()->json($imports);
    }

    /**
     * Upload, validate and store a ZIP import file.
     * This does not run the import. That is performed via a separate endpoint.
     */
    public function upload(Request $request): JsonResponse
    {
        $this->validate($request, $this->rules()['upload']);

        $file = $request->file('file');

        try {
            $import = $this->imports->storeFromUpload($file);
        } catch (ZipValidationException $exception) {
            $message = "ZIP upload failed with the following validation errors: \n" . implode("\n", $exception->errors);
            return $this->jsonError($message, 422);
        }

        return response()->json($import);
    }

    /**
     * Read details of a pending ZIP import.
     */
    public function read(int $id): JsonResponse
    {
        $import = $this->imports->findVisible($id);

        return response()->json($import);
    }

    /**
     * Run the import process for an uploaded ZIP import.
     * The parent_id and parent_type parameters are required when the import type is 'chapter' or 'page'.
     * On success, returns the imported item.
     */
    public function run(int $id, Request $request): JsonResponse
    {
        $import = $this->imports->findVisible($id);
        $parent = null;
        $rules = $this->rules()['run'];

        if ($import->type === 'page' || $import->type === 'chapter') {
            $rules['parent_type'][] = 'required';
            $rules['parent_id'][] = 'required';
            $data = $this->validate($request, $rules);
            $parent = "{$data['parent_type']}:{$data['parent_id']}";
        }

        try {
            $entity = $this->imports->runImport($import, $parent);
        } catch (ZipImportException $exception) {
            $message = "ZIP import failed with the following errors: \n" . implode("\n", $exception->errors);
            return $this->jsonError($message);
        }

        return response()->json($entity);
    }

    /**
     * Delete a pending ZIP import.
     */
    public function delete(int $id): Response
    {
        $import = $this->imports->findVisible($id);
        $this->imports->deleteImport($import);

        return response('', 204);
    }

    protected function rules(): array
    {
        return [
            'upload' => [
                'file' => ['required', ...AttachmentService::getFileValidationRules()],
            ],
            'run' => [
                'parent_type' => ['string', 'in:book,chapter'],
                'parent_id' => ['int'],
            ],
        ];
    }
}
