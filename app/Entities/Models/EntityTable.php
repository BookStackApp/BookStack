<?php

namespace BookStack\Entities\Models;

use BookStack\Activity\Models\Tag;
use BookStack\Activity\Models\View;
use BookStack\App\Model;
use BookStack\Permissions\Models\EntityPermission;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        return $this->hasMany(JointPermission::class, 'entity_id')
            ->whereColumn('entity_type', '=', 'entities.type');
    }

    /**
     * Get the Tags that have been assigned to entities.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'entity_id')
            ->whereColumn('entity_type', '=', 'entities.type');
    }

    /**
     * Get the assigned permissions.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(EntityPermission::class, 'entity_id')
            ->whereColumn('entity_type', '=', 'entities.type');
    }

    /**
     * Get View objects for this entity.
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class, 'viewable_id')
            ->whereColumn('viewable_type', '=', 'entities.type');
    }
}
