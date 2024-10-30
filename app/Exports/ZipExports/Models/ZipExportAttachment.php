<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Exports\ZipExports\ZipExportFiles;
use BookStack\Exports\ZipExports\ZipValidationHelper;
use BookStack\Uploads\Attachment;

class ZipExportAttachment extends ZipExportModel
{
    public ?int $id = null;
    public string $name;
    public ?int $order = null;
    public ?string $link = null;
    public ?string $file = null;

    public static function fromModel(Attachment $model, ZipExportFiles $files): self
    {
        $instance = new self();
        $instance->id = $model->id;
        $instance->name = $model->name;
        $instance->order = $model->order;

        if ($model->external) {
            $instance->link = $model->path;
        } else {
            $instance->file = $files->referenceForAttachment($model);
        }

        return $instance;
    }

    public static function fromModelArray(array $attachmentArray, ZipExportFiles $files): array
    {
        return array_values(array_map(function (Attachment $attachment) use ($files) {
            return self::fromModel($attachment, $files);
        }, $attachmentArray));
    }

    public static function validate(ZipValidationHelper $context, array $data): array
    {
        $rules = [
            'id'    => ['nullable', 'int'],
            'name'  => ['required', 'string', 'min:1'],
            'order' => ['nullable', 'integer'],
            'link'  => ['required_without:file', 'nullable', 'string'],
            'file'  => ['required_without:link', 'nullable', 'string', $context->fileReferenceRule()],
        ];

        return $context->validateArray($data, $rules);
    }
}
