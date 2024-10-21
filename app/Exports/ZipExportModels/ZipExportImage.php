<?php

namespace BookStack\Exports\ZipExportModels;

use BookStack\Activity\Models\Tag;

class ZipExportImage extends ZipExportModel
{
    public string $name;
    public string $file;
}
