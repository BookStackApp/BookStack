<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Activity\Models\Tag;
use BookStack\Exports\ZipExports\ZipValidationHelper;

class ZipExportTag extends ZipExportModel
{
    public string $name;
    public ?string $value = null;

    public function metadataOnly(): void
    {
        $this->value =  null;
    }

    public static function fromModel(Tag $model): self
    {
        $instance = new self();
        $instance->name = $model->name;
        $instance->value = $model->value;

        return $instance;
    }

    public static function fromModelArray(array $tagArray): array
    {
        return array_values(array_map(self::fromModel(...), $tagArray));
    }

    public static function validate(ZipValidationHelper $context, array $data): array
    {
        $rules = [
            'name'  => ['required', 'string', 'min:1'],
            'value' => ['nullable', 'string'],
        ];

        return $context->validateData($data, $rules);
    }

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->name = $data['name'];
        $model->value = $data['value'] ?? null;

        return $model;
    }
}
