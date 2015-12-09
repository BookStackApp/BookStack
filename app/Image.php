<?php

namespace BookStack;


use Images;

class Image
{
    use Ownable;

    protected $fillable = ['name'];

    /**
     * Get a thumbnail for this image.
     * @param  int       $width
     * @param  int       $height
     * @param bool|false $hardCrop
     */
    public function getThumb($width, $height, $hardCrop = false)
    {
        Images::getThumbnail($this, $width, $height, $hardCrop);
    }
}
