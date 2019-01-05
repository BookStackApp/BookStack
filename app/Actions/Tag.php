<?php namespace BookStack\Actions;

use BookStack\Model;

/**
 * Class Attribute
 * @package BookStack
 */
class Tag extends Model
{
    protected $fillable = ['name', 'value', 'order'];

    /**
     * Get the entity that this tag belongs to
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }
}
