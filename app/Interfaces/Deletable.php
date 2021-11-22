<?php

namespace BookStack\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A model that can be deleted in a manner that deletions
 * are tracked to be part of the recycle bin system.
 */
interface Deletable
{
    public function deletions(): MorphMany;
}
