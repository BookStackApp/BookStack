<?php

namespace BookStack\Interfaces;

/**
 * Assigned to models that can have slugs.
 * Must have the below properties.
 *
 * @property int    $id
 * @property string $name
 */
interface Sluggable
{
    /**
     * Regenerate the slug for this model.
     */
    public function refreshSlug(): string;
}
