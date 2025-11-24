<?php

namespace BookStack\Entities\Models;

use BookStack\App\Model;
use BookStack\Permissions\Models\JointPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $sluggable_id
 * @property string $sluggable_type
 * @property string $slug
 * @property ?string $parent_slug
 */
class SlugHistory extends Model
{
    use HasFactory;

    protected $table = 'slug_history';

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'sluggable_id')
            ->whereColumn('joint_permissions.entity_type', '=', 'slug_history.sluggable_type');
    }
}
