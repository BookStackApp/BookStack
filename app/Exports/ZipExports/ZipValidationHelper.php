<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exports\ZipExports\Models\ZipExportModel;
use Illuminate\Validation\Factory;
use ZipArchive;

class ZipValidationHelper
{
    protected Factory $validationFactory;

    public function __construct(
        protected ZipArchive $zip,
    ) {
        $this->validationFactory = app(Factory::class);
    }

    public function validateData(array $data, array $rules): array
    {
        $messages = $this->validationFactory->make($data, $rules)->errors()->messages();

        foreach ($messages as $key => $message) {
            $messages[$key] = implode("\n", $message);
        }

        return $messages;
    }

    public function zipFileExists(string $name): bool
    {
        return $this->zip->statName("files/{$name}") !== false;
    }

    public function fileReferenceRule(): ZipFileReferenceRule
    {
        return new ZipFileReferenceRule($this);
    }

    /**
     * Validate an array of relation data arrays that are expected
     * to be for the given ZipExportModel.
     * @param class-string<ZipExportModel> $model
     */
    public function validateRelations(array $relations, string $model): array
    {
        $results = [];

        foreach ($relations as $key => $relationData) {
            if (is_array($relationData)) {
                $results[$key] = $model::validate($this, $relationData);
            } else {
                $results[$key] = [trans('validation.zip_model_expected', ['type' => gettype($relationData)])];
            }
        }

        return $results;
    }
}
