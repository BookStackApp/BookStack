<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Entities\Models\Entity;

class PermissionFormData
{
    protected Entity $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get the roles with permissions assigned.
     */
    public function rolesWithPermissions(): array
    {
        return $this->entity->permissions()
            ->with('role')
            ->where('role_id', '!=', 0)
            ->get(['id', 'role_id'])
            ->pluck('role')
            ->sortBy('display_name')
            ->all();
    }

    /**
     * Get the roles that don't yet have specific permissions for the
     * entity we're managing permissions for.
     */
    public function rolesNotAssigned(): array
    {
        $assigned = $this->entity->permissions()->pluck('role_id');
        return Role::query()
            ->where('system_name', '!=', 'admin')
            ->whereNotIn('id', $assigned)
            ->orderBy('display_name', 'asc')
            ->get()
            ->all();
    }

    /**
     * Get the "Everyone Else" role entry.
     */
    public function everyoneElseRole(): Role
    {
        return (new Role())->forceFill([
            'id' => 0,
            'display_name' => 'Everyone Else',
            'description' => 'Set permissions for all roles not specifically overridden.'
        ]);
    }
}
