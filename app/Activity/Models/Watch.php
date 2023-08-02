<?php

namespace BookStack\Activity\Models;

use BookStack\Permissions\Models\JointPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function watchable()
    {
        $this->morphTo();
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'watchable_id')
            ->whereColumn('favourites.watchable_type', '=', 'joint_permissions.entity_type');
    }
}
