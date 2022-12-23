<?php

namespace BookStack\Auth\Permissions;

use BookStack\Model;

/**
 * @property int $id
 * @property ?int $role_id
 * @property ?int $user_id
 * @property string $entity_type
 * @property int $entity_id
 * @property bool $view
 */
class CollapsedPermission extends Model
{
    protected $table = 'entity_permissions_collapsed';
}
