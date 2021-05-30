<?php namespace BookStack\Uploads;

use BookStack\Entities\Models\Page;
use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;

class Image extends Model
{
    use HasCreatorAndUpdater;

    protected $fillable = ['name'];
    protected $hidden = [];

    /**
     * Get a thumbnail for this image.
     * @throws \Exception
     */
    public function getThumb(int $width, int $height, bool $keepRatio = false): string
    {
        return app()->make(ImageService::class)->getThumbnail($this, $width, $height, $keepRatio);
    }

    /**
     * Get the page this image has been uploaded to.
     * Only applicable to gallery or drawio image types.
     */
    public function getPage(): ?Page
    {
        return $this->belongsTo(Page::class, 'uploaded_to')->first();
    }
}
