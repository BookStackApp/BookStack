<?php

namespace BookStack\Activity\Models;

use BookStack\Activity\WatchLevels;
use BookStack\Permissions\Models\JointPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $watchable_id
 * @property string $watchable_type
 * @property int $level
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Watch extends Model
{
    protected $guarded = [];

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'watchable_id')
            ->whereColumn('watches.watchable_type', '=', 'joint_permissions.entity_type');
    }

    public function getLevelName(): string
    {
        return WatchLevels::levelValueToName($this->level);
    }

    public function ignoring(): bool
    {
        return $this->level === WatchLevels::IGNORE;
    }
}
