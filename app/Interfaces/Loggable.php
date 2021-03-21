<?php

namespace BookStack\Interfaces;

interface Loggable
{
    /**
     * Get the string descriptor for this item.
     */
    public function logDescriptor(): string;
}
