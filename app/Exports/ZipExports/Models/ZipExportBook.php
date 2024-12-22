<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\ZipExportFiles;
use BookStack\Exports\ZipExports\ZipValidationHelper;

class ZipExportBook extends ZipExportModel
{
    public ?int $id = null;
    public string $name;
    public ?string $description_html = null;
    public ?string $cover = null;
    /** @var ZipExportChapter[] */
    public array $chapters = [];
    /** @var ZipExportPage[] */
    public array $pages = [];
    /** @var ZipExportTag[] */
    public array $tags = [];

    public function metadataOnly(): void
    {
        $this->description_html = $this->cover = null;

        foreach ($this->chapters as $chapter) {
            $chapter->metadataOnly();
        }
        foreach ($this->pages as $page) {
            $page->metadataOnly();
        }
        foreach ($this->tags as $tag) {
            $tag->metadataOnly();
        }
    }

    public function children(): array
    {
        $children = [
            ...$this->pages,
            ...$this->chapters,
        ];

        usort($children, function ($a, $b) {
            return ($a->priority ?? 0) - ($b->priority ?? 0);
        });

        return $children;
    }

    public static function fromModel(Book $model, ZipExportFiles $files): self
    {
        $instance = new self();
        $instance->id = $model->id;
        $instance->name = $model->name;
        $instance->description_html = $model->descriptionHtml();

        if ($model->cover) {
            $instance->cover = $files->referenceForImage($model->cover);
        }

        $instance->tags = ZipExportTag::fromModelArray($model->tags()->get()->all());

        $chapters = [];
        $pages = [];

        $children = $model->getDirectVisibleChildren()->all();
        foreach ($children as $child) {
            if ($child instanceof Chapter) {
                $chapters[] = $child;
            } else if ($child instanceof Page && !$child->draft) {
                $pages[] = $child;
            }
        }

        $instance->pages = ZipExportPage::fromModelArray($pages, $files);
        $instance->chapters = ZipExportChapter::fromModelArray($chapters, $files);

        return $instance;
    }

    public static function validate(ZipValidationHelper $context, array $data): array
    {
        $rules = [
            'id'    => ['nullable', 'int', $context->uniqueIdRule('book')],
            'name'  => ['required', 'string', 'min:1'],
            'description_html' => ['nullable', 'string'],
            'cover' => ['nullable', 'string', $context->fileReferenceRule()],
            'tags' => ['array'],
            'pages' => ['array'],
            'chapters' => ['array'],
        ];

        $errors = $context->validateData($data, $rules);
        $errors['tags'] = $context->validateRelations($data['tags'] ?? [], ZipExportTag::class);
        $errors['pages'] = $context->validateRelations($data['pages'] ?? [], ZipExportPage::class);
        $errors['chapters'] = $context->validateRelations($data['chapters'] ?? [], ZipExportChapter::class);

        return $errors;
    }

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->id = $data['id'] ?? null;
        $model->name = $data['name'];
        $model->description_html = $data['description_html'] ?? null;
        $model->cover = $data['cover'] ?? null;
        $model->tags = ZipExportTag::fromManyArray($data['tags'] ?? []);
        $model->pages = ZipExportPage::fromManyArray($data['pages'] ?? []);
        $model->chapters = ZipExportChapter::fromManyArray($data['chapters'] ?? []);

        return $model;
    }
}
