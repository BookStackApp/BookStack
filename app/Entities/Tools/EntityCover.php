<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class EntityCover
{
    public function __construct(
        protected Book|Bookshelf $entity,
    ) {
    }

    protected function imageQuery(): Builder
    {
        return Image::query()->where('id', '=', $this->entity->image_id);
    }

    /**
     * Check if a cover image exists for this entity.
     */
    public function exists(): bool
    {
        return $this->entity->image_id !== null && $this->imageQuery()->exists();
    }

    /**
     * Get the assigned cover image model.
     */
    public function getImage(): Image|null
    {
        if ($this->entity->image_id === null) {
            return null;
        }

        $cover = $this->imageQuery()->first();
        if ($cover instanceof Image) {
            return $cover;
        }

        return null;
    }

    /**
     * Returns a cover image URL, or the given default if none assigned/existing.
     */
    public function getUrl(int $width = 440, int $height = 250, string|null $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='): string|null
    {
        if (!$this->entity->image_id) {
            return $default;
        }

        try {
            return $this->getImage()?->getThumb($width, $height, false) ?? $default;
        } catch (Exception $err) {
            return $default;
        }
    }

    /**
     * Set the image to use as the cover for this entity.
     */
    public function setImage(Image|null $image): void
    {
        if ($image === null) {
            $this->entity->image_id = null;
        } else {
            $this->entity->image_id = $image->id;
        }
    }
}
