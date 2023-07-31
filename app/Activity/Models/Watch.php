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
    protected static array $levelByOption = [
        'default' => -1,
        'ignore' => 0,
        'new' => 1,
        'updates' => 2,
        'comments' => 3,
    ];

    public function watchable()
    {
        $this->morphTo();
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'watchable_id')
            ->whereColumn('favourites.watchable_type', '=', 'joint_permissions.entity_type');
    }

    /**
     * @return string[]
     */
    public static function getAvailableOptionNames(): array
    {
        return array_keys(static::$levelByOption);
    }

    public static function optionNameToLevel(string $option): int
    {
        return static::$levelByOption[$option] ?? -1;
    }
}
