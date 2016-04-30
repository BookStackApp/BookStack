<?php namespace BookStack;


abstract class Ownable extends Model
{
    /**
     * Relation for the user that created this entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('BookStack\User', 'created_by');
    }

    /**
     * Relation for the user that updated this entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo('BookStack\User', 'updated_by');
    }

    /**
     * Gets the class name.
     * @return string
     */
    public static function getClassName()
    {
        return strtolower(array_slice(explode('\\', static::class), -1, 1)[0]);
    }

}