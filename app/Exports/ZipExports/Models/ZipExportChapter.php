<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\ZipExportFiles;

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
}
