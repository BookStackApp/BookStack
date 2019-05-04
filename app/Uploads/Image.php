<?php namespace BookStack\Uploads;

use BookStack\Entities\Page;
use BookStack\Ownable;
use Images;

class Image extends Ownable
{

    protected $fillable = ['name'];

    /**
     * Get a thumbnail for this image.
     * @param  int $width
     * @param  int $height
     * @param bool|false $keepRatio
     * @return string
     * @throws \Exception
     */
    public function getThumb($width, $height, $keepRatio = false)
    {
        return Images::getThumbnail($this, $width, $height, $keepRatio);
    }

    /**
     * Get the page this image has been uploaded to.
     * Only applicable to gallery or drawio image types.
     * @return Page|null
     */
    public function getPage()
    {
        return $this->belongsTo(Page::class, 'uploaded_to')->first();
    }
}
