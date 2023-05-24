<?php

namespace BookStack\Activity\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Viewable
{
    /**
     * Get all view instances for this viewable model.
     */
    public function views(): MorphMany;
}
