<?php namespace BookStack;

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
     * Get the revisions for this image.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions()
    {
        return $this->hasMany(ImageRevision::class);
    }

    /**
     * Get the count of revisions made to this image.
     * Based off numbers on revisions rather than raw count of attached revisions
     * as they may be cleared up or revisions deleted at some point.
     * @return int
     */
    public function revisionCount()
    {
        return intval($this->revisions()->max('revision'));
    }
}
