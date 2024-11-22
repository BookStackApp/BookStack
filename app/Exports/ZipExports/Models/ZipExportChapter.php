<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\ZipExportFiles;
use BookStack\Exports\ZipExports\ZipValidationHelper;

class ZipExportChapter extends ZipExportModel
{
    public ?int $id = null;
    public string $name;
    public ?string $description_html = null;
    public ?int $priority = null;
    /** @var ZipExportPage[] */
    public array $pages = [];
    /** @var ZipExportTag[] */
    public array $tags = [];

    public function metadataOnly(): void
    {
        $this->description_html = null;

        foreach ($this->pages as $page) {
            $page->metadataOnly();
        }
        foreach ($this->tags as $tag) {
            $tag->metadataOnly();
        }
    }

    public function children(): array
    {
        return $this->pages;
    }

    public static function fromModel(Chapter $model, ZipExportFiles $files): self
    {
        $instance = new self();
        $instance->id = $model->id;
        $instance->name = $model->name;
        $instance->description_html = $model->descriptionHtml();
        $instance->priority = $model->priority;
        $instance->tags = ZipExportTag::fromModelArray($model->tags()->get()->all());

        $pages = $model->getVisiblePages()->filter(fn (Page $page) => !$page->draft)->all();
        $instance->pages = ZipExportPage::fromModelArray($pages, $files);

        return $instance;
    }

    /**
     * @param Chapter[] $chapterArray
     * @return self[]
     */
    public static function fromModelArray(array $chapterArray, ZipExportFiles $files): array
    {
        return array_values(array_map(function (Chapter $chapter) use ($files) {
            return self::fromModel($chapter, $files);
        }, $chapterArray));
    }

    public static function validate(ZipValidationHelper $context, array $data): array
    {
        $rules = [
            'id'    => ['nullable', 'int', $context->uniqueIdRule('chapter')],
            'name'  => ['required', 'string', 'min:1'],
            'description_html' => ['nullable', 'string'],
            'priority' => ['nullable', 'int'],
            'tags' => ['array'],
            'pages' => ['array'],
        ];

        $errors = $context->validateData($data, $rules);
        $errors['tags'] = $context->validateRelations($data['tags'] ?? [], ZipExportTag::class);
        $errors['pages'] = $context->validateRelations($data['pages'] ?? [], ZipExportPage::class);

        return $errors;
    }

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->id = $data['id'] ?? null;
        $model->name = $data['name'];
        $model->description_html = $data['description_html'] ?? null;
        $model->priority = isset($data['priority']) ? intval($data['priority']) : null;
        $model->tags = ZipExportTag::fromManyArray($data['tags'] ?? []);
        $model->pages = ZipExportPage::fromManyArray($data['pages'] ?? []);

        return $model;
    }
}
