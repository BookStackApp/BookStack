<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Exports\ZipExports\ZipExportFiles;
use BookStack\Exports\ZipExports\ZipValidationHelper;
use BookStack\Uploads\Image;
use Illuminate\Validation\Rule;

class ZipExportImage extends ZipExportModel
{
    public ?int $id = null;
    public string $name;
    public string $file;
    public string $type;

    public static function fromModel(Image $model, ZipExportFiles $files): self
    {
        $instance = new self();
        $instance->id = $model->id;
        $instance->name = $model->name;
        $instance->type = $model->type;
        $instance->file = $files->referenceForImage($model);

        return $instance;
    }

    public function metadataOnly(): void
    {
        //
    }

    public static function validate(ZipValidationHelper $context, array $data): array
    {
        $acceptedImageTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
        $rules = [
            'id'    => ['nullable', 'int', $context->uniqueIdRule('image')],
            'name'  => ['required', 'string', 'min:1'],
            'file'  => ['required', 'string', $context->fileReferenceRule($acceptedImageTypes)],
            'type'  => ['required', 'string', Rule::in(['gallery', 'drawio'])],
        ];

        return $context->validateData($data, $rules);
    }

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->id = $data['id'] ?? null;
        $model->name = $data['name'];
        $model->file = $data['file'];
        $model->type = $data['type'];

        return $model;
    }
}
