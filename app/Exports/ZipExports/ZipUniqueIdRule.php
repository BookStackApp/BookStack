<?php

namespace BookStack\Exports\ZipExports;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ZipUniqueIdRule implements ValidationRule
{
    public function __construct(
        protected ZipValidationHelper $context,
        protected string $modelType,
    ) {
    }


    /**
     * @inheritDoc
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->context->hasIdBeenUsed($this->modelType, $value)) {
            $fail('validation.zip_unique')->translate(['attribute' => $attribute]);
        }
    }
}
