<?php

namespace BookStack\Exports\ZipExportModels;

use BookStack\Activity\Models\Tag;

class ZipExportImage implements ZipExportModel
{
    public string $name;
    public string $file;
}
