<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasCoverImage
{
    /**
     * Get the cover image for this item.
     */
    public function cover(): BelongsTo;

    /**
     * Get the type of the image model that is used when storing a cover image.
     */
    public function coverImageTypeKey(): string;
}
