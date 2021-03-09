<?php namespace BookStack\Interfaces;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface Sluggable
 *
 * Assigned to models that can have slugs.
 * Must have the below properties.
 *
 * @property int $id
 * @property string $name
 * @method Builder newQuery
 */
interface Sluggable
{

    /**
     * Regenerate the slug for this model.
     */
    public function refreshSlug(): string;

}