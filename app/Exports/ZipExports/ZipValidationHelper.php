<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exports\ZipExports\Models\ZipExportModel;
use Illuminate\Validation\Factory;

class ZipValidationHelper
{
    protected Factory $validationFactory;

    /**
     * Local store of validated IDs (in format "<type>:<id>". Example: "book:2")
     * which we can use to check uniqueness.
     * @var array<string, bool>
     */
    protected array $validatedIds = [];

    public function __construct(
        public ZipExportReader $zipReader,
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

    public function fileReferenceRule(array $acceptedMimes = []): ZipFileReferenceRule
    {
        return new ZipFileReferenceRule($this, $acceptedMimes);
    }

    public function uniqueIdRule(string $type): ZipUniqueIdRule
    {
        return new ZipUniqueIdRule($this, $type);
    }

    public function hasIdBeenUsed(string $type, mixed $id): bool
    {
        $key = $type . ':' . $id;
        if (isset($this->validatedIds[$key])) {
            return true;
        }

        $this->validatedIds[$key] = true;

        return false;
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
