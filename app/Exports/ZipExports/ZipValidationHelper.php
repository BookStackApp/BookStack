<?php

namespace BookStack\Exports\ZipExports;

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

    public function validateArray(array $data, array $rules): array
    {
        return $this->validationFactory->make($data, $rules)->errors()->messages();
    }

    public function zipFileExists(string $name): bool
    {
        return $this->zip->statName("files/{$name}") !== false;
    }

    public function fileReferenceRule(): ZipFileReferenceRule
    {
        return new ZipFileReferenceRule($this);
    }
}
