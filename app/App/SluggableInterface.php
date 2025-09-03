<?php

namespace BookStack\App;

/**
 * Assigned to models that can have slugs.
 * Must have the below properties.
 */
interface SluggableInterface
{
    /**
     * Regenerate the slug for this model.
     */
    public function refreshSlug(): string;
}
