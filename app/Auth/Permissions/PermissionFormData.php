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
            ->whereNotNull('role_id')
            ->get()
            ->sortBy('role.display_name')
            ->all();
    }

    /**
     * Get the permissions with assigned users.
     */
    public function permissionsWithUsers(): array
    {
        return $this->entity->permissions()
            ->with('user')
            ->whereNotNull('user_id')
            ->get()
            ->sortBy('user.name')
            ->all();
    }

    /**
     * Get the roles that don't yet have specific permissions for the
     * entity we're managing permissions for.
     */
    public function rolesNotAssigned(): array
    {
        $assigned = $this->entity->permissions()->whereNotNull('role_id')->pluck('role_id');
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
        /** @var ?EntityPermission $permission */
        $permission = $this->entity->permissions()
            ->whereNull(['role_id', 'user_id'])
            ->first();
        return $permission ?? (new EntityPermission());
    }

    /**
     * Check if the "Everyone else" option is inheriting default role system permissions.
     * Is determined by any system entity_permission existing for the current entity.
     */
    public function everyoneElseInheriting(): bool
    {
        return !$this->entity->permissions()
            ->whereNull(['role_id', 'user_id'])
            ->exists();
    }
}
