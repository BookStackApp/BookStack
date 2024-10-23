<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\ZipExportFiles;

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
            } else if ($child instanceof Page) {
                $pages[] = $child;
            }
        }

        $instance->pages = ZipExportPage::fromModelArray($pages, $files);
        $instance->chapters = ZipExportChapter::fromModelArray($chapters, $files);

        return $instance;
    }
}
