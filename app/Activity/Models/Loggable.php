<?php

namespace BookStack\Activity\Models;

interface Loggable
{
    /**
     * Get the string descriptor for this item.
     */
    public function logDescriptor(): string;
}
