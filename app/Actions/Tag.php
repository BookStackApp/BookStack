<?php namespace BookStack\Actions;

use BookStack\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'value', 'order'];
    protected $hidden = ['id', 'entity_id', 'entity_type', 'created_at', 'updated_at'];

    /**
     * Get the entity that this tag belongs to
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }
}
