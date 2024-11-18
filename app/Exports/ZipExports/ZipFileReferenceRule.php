<?php

namespace BookStack\Exports\ZipExports;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ZipFileReferenceRule implements ValidationRule
{
    public function __construct(
        protected ZipValidationHelper $context,
        protected array $acceptedMimes,
    ) {
    }


    /**
     * @inheritDoc
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->context->zipReader->fileExists($value)) {
            $fail('validation.zip_file')->translate();
        }

        if (!empty($this->acceptedMimes)) {
            $fileMime = $this->context->zipReader->sniffFileMime($value);
            if (!in_array($fileMime, $this->acceptedMimes)) {
                $fail('validation.zip_file_mime')->translate([
                    'attribute' => $attribute,
                    'validTypes' => implode(',', $this->acceptedMimes),
                    'foundType' => $fileMime
                ]);
            }
        }
    }
}
