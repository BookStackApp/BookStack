<?php

namespace BookStack\References;

use BookStack\Auth\Permissions\JointPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int    $from_id
 * @property string $from_type
 * @property int    $to_id
 * @property string $to_type
 */
class Reference extends Model
{
    public $timestamps = false;

    public function from(): MorphTo
    {
        return $this->morphTo('from');
    }

    public function to(): MorphTo
    {
        return $this->morphTo('to');
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'from_id')
            ->whereColumn('references.from_type', '=', 'joint_permissions.entity_type');
    }
}
