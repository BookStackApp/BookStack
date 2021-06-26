<?php

namespace BookStack\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Viewable
{
    /**
     * Get all view instances for this viewable model.
     */
    public function views(): MorphMany;
}
