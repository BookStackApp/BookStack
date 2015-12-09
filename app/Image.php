<?php

namespace BookStack;


use Illuminate\Database\Eloquent\Model;
use Images;

class Image extends Model
{
    use Ownable;

    protected $fillable = ['name'];

    /**
     * Get a thumbnail for this image.
     * @param  int       $width
     * @param  int       $height
     * @param bool|false $hardCrop
     * @return string
     */
    public function getThumb($width, $height, $hardCrop = false)
    {
        return Images::getThumbnail($this, $width, $height, $hardCrop);
    }
}
