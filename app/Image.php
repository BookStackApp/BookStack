<?php

namespace Oxbow;


class Image extends Entity
{

    protected $fillable = ['name'];

    public function getFilePath()
    {
        return storage_path() . $this->url;
    }

}
