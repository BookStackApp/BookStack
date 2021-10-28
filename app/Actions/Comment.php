<?php

namespace BookStack\Actions;

use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int      $id
 * @property string   $text
 * @property string   $html
 * @property int|null $parent_id
 * @property int      $local_id
 */
class Comment extends Model
{
    use HasFactory;
    use HasCreatorAndUpdater;

    protected $fillable = ['text', 'parent_id'];
    protected $appends = ['created', 'updated'];

    /**
     * Get the entity that this comment belongs to.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Check if a comment has been updated since creation.
     */
    public function isUpdated(): bool
    {
        return $this->updated_at->timestamp > $this->created_at->timestamp;
    }

    /**
     * Get created date as a relative diff.
     *
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get updated date as a relative diff.
     *
     * @return mixed
     */
    public function getUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}
