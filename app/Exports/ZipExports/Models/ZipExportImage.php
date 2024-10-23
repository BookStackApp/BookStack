<?php

namespace BookStack\Exports\ZipExports\Models;

use BookStack\Exports\ZipExports\ZipExportFiles;
use BookStack\Uploads\Image;

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
}
