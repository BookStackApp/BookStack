<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exports\ZipExports\ZipExportFiles;

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
}
