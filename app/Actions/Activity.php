<?php

namespace BookStack\Actions;

use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use BookStack\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property string $type
 * @property User $user
 * @property Entity $entity
 * @property string $detail
 * @property string $entity_type
 * @property int $entity_id
 * @property int $user_id
 */
class Activity extends Model
{

    /**
     * Get the entity for this activity.
     */
    public function entity(): MorphTo
    {
        if ($this->entity_type === '') {
            $this->entity_type = null;
        }
        return $this->morphTo('entity');
    }

    /**
     * Get the user this activity relates to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns text from the language files, Looks up by using the activity key.
     */
    public function getText(): string
    {
        return trans('activities.' . $this->type);
    }

    /**
     * Check if this activity is intended to be for an entity.
     */
    public function isForEntity(): bool
    {
        return Str::startsWith($this->type, [
            'page_', 'chapter_', 'book_', 'bookshelf_'
        ]);
    }

    /**
     * Checks if another Activity matches the general information of another.
     */
    public function isSimilarTo(Activity $activityB): bool
    {
        return [$this->type, $this->entity_type, $this->entity_id] === [$activityB->type, $activityB->entity_type, $activityB->entity_id];
    }
}
