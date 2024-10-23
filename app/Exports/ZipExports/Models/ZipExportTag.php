<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Activity\Models\Tag;

class ZipExportTag extends ZipExportModel
{
    public string $name;
    public ?string $value = null;
    public ?int $order = null;

    public static function fromModel(Tag $model): self
    {
        $instance = new self();
        $instance->name = $model->name;
        $instance->value = $model->value;
        $instance->order = $model->order;

        return $instance;
    }

    public static function fromModelArray(array $tagArray): array
    {
        return array_values(array_map(self::fromModel(...), $tagArray));
    }
}
