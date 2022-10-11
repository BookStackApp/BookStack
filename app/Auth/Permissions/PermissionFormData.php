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
     * Get the permissions with assigned roles.
     */
    public function permissionsWithRoles(): array
    {
        return $this->entity->permissions()
            ->with('role')
            ->where('role_id', '!=', 0)
            ->get()
            ->sortBy('role.display_name')
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
     * Get the entity permission for the "Everyone Else" option.
     */
    public function everyoneElseEntityPermission(): EntityPermission
    {
        /** @var EntityPermission $permission */
        $permission = $this->entity->permissions()
            ->where('role_id', '=', 0)
            ->first();
        return $permission ?? (new EntityPermission());
    }

    /**
     * Get the "Everyone Else" role entry.
     */
    public function everyoneElseRole(): Role
    {
        return (new Role())->forceFill([
            'id' => 0,
            'display_name' => trans('entities.permissions_role_everyone_else'),
            'description' => trans('entities.permissions_role_everyone_else_desc'),
        ]);
    }
}
