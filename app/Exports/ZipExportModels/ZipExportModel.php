<?php

namespace BookStack\Exports\ZipExportModels;

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
}
