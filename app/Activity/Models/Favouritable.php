<?php

namespace BookStack\Activity\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Favouritable
{
    /**
     * Get the related favourite instances.
     */
    public function favourites(): MorphMany;
}
