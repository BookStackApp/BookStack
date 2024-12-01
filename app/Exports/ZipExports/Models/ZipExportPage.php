<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exports\ZipExports\ZipExportFiles;
use BookStack\Exports\ZipExports\ZipValidationHelper;

class ZipExportPage extends ZipExportModel
{
    public ?int $id = null;
    public string $name;
    public ?string $html = null;
    public ?string $markdown = null;
    public ?int $priority = null;
    /** @var ZipExportAttachment[] */
    public array $attachments = [];
    /** @var ZipExportImage[] */
    public array $images = [];
    /** @var ZipExportTag[] */
    public array $tags = [];

    public function metadataOnly(): void
    {
        $this->html = $this->markdown = null;

        foreach ($this->attachments as $attachment) {
            $attachment->metadataOnly();
        }
        foreach ($this->images as $image) {
            $image->metadataOnly();
        }
        foreach ($this->tags as $tag) {
            $tag->metadataOnly();
        }
    }

    public static function fromModel(Page $model, ZipExportFiles $files): self
    {
        $instance = new self();
        $instance->id = $model->id;
        $instance->name = $model->name;
        $instance->html = (new PageContent($model))->render();
        $instance->priority = $model->priority;

        if (!empty($model->markdown)) {
            $instance->markdown = $model->markdown;
        }

        $instance->tags = ZipExportTag::fromModelArray($model->tags()->get()->all());
        $instance->attachments = ZipExportAttachment::fromModelArray($model->attachments()->get()->all(), $files);

        return $instance;
    }

    /**
     * @param Page[] $pageArray
     * @return self[]
     */
    public static function fromModelArray(array $pageArray, ZipExportFiles $files): array
    {
        return array_values(array_map(function (Page $page) use ($files) {
            return self::fromModel($page, $files);
        }, $pageArray));
    }

    public static function validate(ZipValidationHelper $context, array $data): array
    {
        $rules = [
            'id'    => ['nullable', 'int', $context->uniqueIdRule('page')],
            'name'  => ['required', 'string', 'min:1'],
            'html' => ['nullable', 'string'],
            'markdown' => ['nullable', 'string'],
            'priority' => ['nullable', 'int'],
            'attachments' => ['array'],
            'images' => ['array'],
            'tags' => ['array'],
        ];

        $errors = $context->validateData($data, $rules);
        $errors['attachments'] = $context->validateRelations($data['attachments'] ?? [], ZipExportAttachment::class);
        $errors['images'] = $context->validateRelations($data['images'] ?? [], ZipExportImage::class);
        $errors['tags'] = $context->validateRelations($data['tags'] ?? [], ZipExportTag::class);

        return $errors;
    }

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->id = $data['id'] ?? null;
        $model->name = $data['name'];
        $model->html = $data['html'] ?? null;
        $model->markdown = $data['markdown'] ?? null;
        $model->priority = isset($data['priority']) ? intval($data['priority']) : null;
        $model->attachments = ZipExportAttachment::fromManyArray($data['attachments'] ?? []);
        $model->images = ZipExportImage::fromManyArray($data['images'] ?? []);
        $model->tags = ZipExportTag::fromManyArray($data['tags'] ?? []);

        return $model;
    }
}
