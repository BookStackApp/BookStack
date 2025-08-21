<?php

declare(strict_types=1);

namespace BookStack\Search\Queries;

use BookStack\Permissions\Models\JointPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $entity_type
 * @property int $entity_id
 * @property string $text
 * @property string $embedding
 */
class SearchVector extends Model
{
    public $timestamps = false;

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'entity_id')
            ->whereColumn('search_vectors.entity_type', '=', 'joint_permissions.entity_type');
    }
}
