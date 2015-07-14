<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function getFilePath()
    {
        return storage_path() . $this->url;
    }
}
