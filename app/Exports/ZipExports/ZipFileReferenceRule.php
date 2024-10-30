<?php

namespace BookStack\Exports\ZipExports;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ZipArchive;

class ZipFileReferenceRule implements ValidationRule
{
    public function __construct(
        protected ZipValidationHelper $context,
    ) {
    }


    /**
     * @inheritDoc
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->context->zipFileExists($value)) {
            $fail('validation.zip_file')->translate();
        }
    }
}
