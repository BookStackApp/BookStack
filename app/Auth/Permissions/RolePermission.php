<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 */
class RolePermission extends Model
{
    /**
     * The roles that belong to the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    /**
     * Get the permission object by name.
     */
    public static function getByName(string $name): ?RolePermission
    {
        return static::where('name', '=', $name)->first();
    }
}
