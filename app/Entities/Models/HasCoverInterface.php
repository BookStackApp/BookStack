<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityCover;
use BookStack\Uploads\Image;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasCoverInterface
{
    public function coverInfo(): EntityCover;

    /**
     * The cover image of this entity.
     * @return BelongsTo<Image, covariant Entity>
     */
    public function cover(): BelongsTo;
}
