<?php

namespace BookStack\Exports\ZipExportModels;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exports\ZipExportFiles;

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

        if (!empty($model->markdown)) {
            $instance->markdown = $model->markdown;
        }

        $instance->tags = ZipExportTag::fromModelArray($model->tags()->get()->all());
        $instance->attachments = ZipExportAttachment::fromModelArray($model->attachments()->get()->all(), $files);

        return $instance;
    }
}
