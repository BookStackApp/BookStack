<?php

namespace BookStack\Auth\Permissions;

use BookStack\Entities\Models\Entity;
use BookStack\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Holds the "cached" user-specific permissions for entities in the system.
 * These only exist to indicate resolved permissions active via user-specific
 * entity permissions, not for all permission combinations for all users.
 *
 * @property int $user_id
 * @property int $entity_id
 * @property string $entity_type
 * @property boolean $has_permission
 */
class JointUserPermission extends Model
{
    protected $primaryKey = null;
    public $timestamps = false;

    /**
     * Get the entity this points to.
     */
    public function entity(): MorphOne
    {
        return $this->morphOne(Entity::class, 'entity');
    }
}
