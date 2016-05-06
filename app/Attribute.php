<?php namespace BookStack;

/**
 * Class Attribute
 * @package BookStack
 */
class Attribute extends Model
{
    protected $fillable = ['name', 'value'];

    /**
     * Get the entity that this attribute belongs to
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }
}