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
     * Requires permission to import content.
     */
    public function list(): JsonResponse
    {
        $query = $this->imports->queryVisible();

        return $this->apiListingResponse($query, [
            'id', 'name', 'size', 'type', 'created_by', 'created_at', 'updated_at'
        ]);
    }

    /**
     * Start a new import from a ZIP file.
     * This does not actually run the import since that is performed via the "run" endpoint.
     * This uploads, validates and stores the ZIP file so it's ready to be imported.
     *
     * This "file" parameter must be a BookStack-compatible ZIP file, and this must be
     * sent via a 'multipart/form-data' type request.
     *
     * Requires permission to import content.
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, $this->rules()['create']);

        $file = $request->file('file');

        try {
            $import = $this->imports->storeFromUpload($file);
        } catch (ZipValidationException $exception) {
            $message = "ZIP upload failed with the following validation errors: \n" . $this->formatErrors($exception->errors);
            return $this->jsonError($message, 422);
        }

        return response()->json($import);
    }

    /**
     * Read details of a pending ZIP import.
     * The "details" property contains high-level metadata regarding the ZIP import content,
     * and the structure of this will change depending on import "type".
     * Requires permission to import content.
     */
    public function read(int $id): JsonResponse
    {
        $import = $this->imports->findVisible($id);

        $import->setAttribute('details', $import->decodeMetadata());

        return response()->json($import);
    }

    /**
     * Run the import process for an uploaded ZIP import.
     * The "parent_id" and "parent_type" parameters are required when the import type is "chapter" or "page".
     * On success, this endpoint returns the imported item.
     * Requires permission to import content.
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
            $message = "ZIP import failed with the following errors: \n" . $this->formatErrors($exception->errors);
            return $this->jsonError($message);
        }

        return response()->json($entity->withoutRelations());
    }

    /**
     * Delete a pending ZIP import from the system.
     * Requires permission to import content.
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
            'create' => [
                'file' => ['required', ...AttachmentService::getFileValidationRules()],
            ],
            'run' => [
                'parent_type' => ['string', 'in:book,chapter'],
                'parent_id' => ['int'],
            ],
        ];
    }

    protected function formatErrors(array $errors): string
    {
        $parts = [];
        foreach ($errors as $key => $error) {
            if (is_string($key)) {
                $parts[] = "[{$key}] {$error}";
            } else {
                $parts[] = $error;
            }
        }
        return implode("\n", $parts);
    }
}
