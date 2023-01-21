<?php

namespace Tests\Helpers;

use BookStack\Auth\Permissions\EntityPermission;
use BookStack\Auth\Permissions\RolePermission;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;

class PermissionsProvider
{
    protected UserRoleProvider $userRoleProvider;

    public function __construct(UserRoleProvider $userRoleProvider)
    {
        $this->userRoleProvider = $userRoleProvider;
    }

    /**
     * Grant role permissions to the provided user.
     */
    public function grantUserRolePermissions(User $user, array $permissions): void
    {
        $newRole = $this->userRoleProvider->createRole($permissions);
        $user->attachRole($newRole);
        $user->load('roles');
        $user->clearPermissionCache();
    }

    /**
     * Completely remove specific role permissions from the provided user.
     */
    public function removeUserRolePermissions(User $user, array $permissions): void
    {
        foreach ($permissions as $permissionName) {
            /** @var RolePermission $permission */
            $permission = RolePermission::query()
                ->where('name', '=', $permissionName)
                ->firstOrFail();

            $roles = $user->roles()->whereHas('permissions', function ($query) use ($permission) {
                $query->where('id', '=', $permission->id);
            })->get();

            /** @var Role $role */
            foreach ($roles as $role) {
                $role->detachPermission($permission);
            }

            $user->clearPermissionCache();
        }
    }

    /**
     * Change the owner of the given entity to the given user.
     */
    public function changeEntityOwner(Entity $entity, User $newOwner): void
    {
        $entity->owned_by = $newOwner->id;
        $entity->save();
        $entity->rebuildPermissions();
    }

    /**
     * Regenerate the permission for an entity.
     * Centralised to manage clearing of cached elements between requests.
     */
    public function regenerateForEntity(Entity $entity): void
    {
        $entity->rebuildPermissions();
    }

    /**
     * Set the given entity as having restricted permissions, and apply the given
     * permissions for the given roles.
     * @param string[] $actions
     * @param Role[] $roles
     */
    public function setEntityPermissions(Entity $entity, array $actions = [], array $roles = [], $inherit = false): void
    {
        $entity->permissions()->delete();

        $permissions = [];

        if (!$inherit) {
            // Set default permissions to not allow actions so that only the provided role permissions are at play.
            $permissions[] = ['role_id' => 0, 'view' => false, 'create' => false, 'update' => false, 'delete' => false];
        }

        foreach ($roles as $role) {
            $permissions[] = $this->actionListToEntityPermissionData($actions, $role->id);
        }

        $this->addEntityPermissionEntries($entity, $permissions);
    }

    public function addEntityPermission(Entity $entity, array $actionList, Role $role)
    {
        $permissionData = $this->actionListToEntityPermissionData($actionList, $role->id);
        $this->addEntityPermissionEntries($entity, [$permissionData]);
    }

    /**
     * Disable inherited permissions on the given entity.
     * Effectively sets the "Other Users" UI permission option to not inherit, with no permissions.
     */
    public function disableEntityInheritedPermissions(Entity $entity): void
    {
        $entity->permissions()->where('role_id', '=', 0)->delete();
        $fallback = $this->actionListToEntityPermissionData([]);
        $this->addEntityPermissionEntries($entity, [$fallback]);
    }

    protected function addEntityPermissionEntries(Entity $entity, array $entityPermissionData): void
    {
        $entity->permissions()->createMany($entityPermissionData);
        $entity->load('permissions');
        $this->regenerateForEntity($entity);
    }

    /**
     * For the given simple array of string actions (view, create, update, delete), convert
     * the format to entity permission data, where permission is granted if the action is in the
     * given actionList array.
     */
    protected function actionListToEntityPermissionData(array $actionList, int $roleId = 0): array
    {
        $permissionData = ['role_id' => $roleId];
        foreach (EntityPermission::PERMISSIONS as $possibleAction) {
            $permissionData[$possibleAction] = in_array($possibleAction, $actionList);
        }

        return $permissionData;
    }
}
