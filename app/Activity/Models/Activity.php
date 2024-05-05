<?php

namespace BookStack\Activity\Models;

use BookStack\App\Model;
use BookStack\Entities\Models\Entity;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $type
 * @property User   $user
 * @property Entity $loggable
 * @property string $detail
 * @property string $loggable_type
 * @property int    $loggable_id
 * @property int    $user_id
 * @property Carbon $created_at
 */
class Activity extends Model
{
    /**
     * Get the loggable model related to this activity.
     * Currently only used for entities (previously entity_[id/type] columns).
     * Could be used for others but will need an audit of uses where assumed
     * to be entities.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo('loggable');
    }

    /**
     * Get the user this activity relates to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'loggable_id')
            ->whereColumn('activities.loggable_type', '=', 'joint_permissions.entity_type');
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
            'page_', 'chapter_', 'book_', 'bookshelf_',
        ]);
    }

    /**
     * Checks if another Activity matches the general information of another.
     */
    public function isSimilarTo(self $activityB): bool
    {
        return [$this->type, $this->loggable_type, $this->loggable_id] === [$activityB->type, $activityB->loggable_type, $activityB->loggable_id];
    }
}
