<?php

namespace BookStack\Actions;

use BookStack\Auth\Permissions\JointPermission;
use BookStack\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favourite extends Model
{
    protected $fillable = ['user_id'];

    /**
     * Get the related model that can be favourited.
     */
    public function favouritable(): MorphTo
    {
        return $this->morphTo();
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'favouritable_id')
            ->whereColumn('favourites.favouritable_type', '=', 'joint_permissions.entity_type');
    }
}
