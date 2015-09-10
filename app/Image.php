<?php

namespace BookStack;


class Image extends Entity
{

    protected $fillable = ['name'];

    public function getFilePath()
    {
        return storage_path() . $this->url;
    }

    /**
     * Get the url for this item.
     * @return string
     */
    public function getUrl()
    {
        return public_path() . $this->url;
    }
}
