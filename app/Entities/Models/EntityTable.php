<?php

namespace BookStack\Entities\Models;

use BookStack\App\Model;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is a simplistic model interpretation of a generic Entity used to query and represent
 * that database abstractly. Generally, this should rarely be used outside queries.
 */
class EntityTable extends Model
{
    use SoftDeletes;

    protected $table = 'entities';

    /**
     * Get the entities that are visible to the current user.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return app()->make(PermissionApplicator::class)->restrictEntityQuery($query);
    }

    /**
     * Get the entity jointPermissions this is connected to.
     */
    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id')->whereColumn('entity_type', '=', 'entities.type');
    }
}
