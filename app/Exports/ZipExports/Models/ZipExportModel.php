<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Exports\ZipExports\ZipValidationHelper;
use JsonSerializable;

abstract class ZipExportModel implements JsonSerializable
{
    /**
     * Handle the serialization to JSON.
     * For these exports, we filter out optional (represented as nullable) fields
     * just to clean things up and prevent confusion to avoid null states in the
     * resulting export format itself.
     */
    public function jsonSerialize(): array
    {
        $publicProps = get_object_vars(...)->__invoke($this);
        return array_filter($publicProps, fn ($value) => $value !== null);
    }

    /**
     * Validate the given array of data intended for this model.
     * Return an array of validation errors messages.
     * Child items can be considered in the validation result by returning a keyed
     * item in the array for its own validation messages.
     */
    abstract public static function validate(ZipValidationHelper $context, array $data): array;
}
