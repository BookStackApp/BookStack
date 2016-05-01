<?php namespace BookStack;

class JointPermission extends Model
{
    public $timestamps = false;

    /**
     * Get the role that this points to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the entity this points to.
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function entity()
    {
        return $this->morphOne(Entity::class, 'entity');
    }
}
