<?php namespace BookStack\Actions;

use BookStack\Ownable;

class Comment extends Ownable
{
    protected $fillable = ['text', 'html', 'parent_id'];
    protected $appends = ['created', 'updated'];

    /**
     * Get the entity that this comment belongs to
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }

    /**
     * Check if a comment has been updated since creation.
     * @return bool
     */
    public function isUpdated()
    {
        return $this->updated_at->timestamp > $this->created_at->timestamp;
    }

    /**
     * Get created date as a relative diff.
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get updated date as a relative diff.
     * @return mixed
     */
    public function getUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}
