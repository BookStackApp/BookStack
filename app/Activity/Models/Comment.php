<?php

namespace BookStack\Activity\Models;

use BookStack\App\Model;
use BookStack\Users\Models\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int      $id
 * @property string   $text
 * @property string   $html
 * @property int|null $parent_id  - Relates to local_id, not id
 * @property int      $local_id
 * @property string   $entity_type
 * @property int      $entity_id
 * @property int      $created_by
 * @property int      $updated_by
 */
class Comment extends Model implements Loggable
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
     * Get the parent comment this is in reply to (if existing).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'local_id', 'parent')
            ->where('entity_type', '=', $this->entity_type)
            ->where('entity_id', '=', $this->entity_id);
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
     */
    public function getCreatedAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get updated date as a relative diff.
     */
    public function getUpdatedAttribute(): string
    {
        return $this->updated_at->diffForHumans();
    }

    public function logDescriptor(): string
    {
        return "Comment #{$this->local_id} (ID: {$this->id}) for {$this->entity_type} (ID: {$this->entity_id})";
    }
}
