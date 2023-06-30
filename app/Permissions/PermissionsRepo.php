<?php

namespace BookStack\Permissions;

use BookStack\Activity\ActivityType;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use BookStack\Permissions\Models\RolePermission;
use BookStack\Users\Models\Role;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PermissionsRepo
{
    protected JointPermissionBuilder $permissionBuilder;
    protected array $systemRoles = ['admin', 'public'];

    public function __construct(JointPermissionBuilder $permissionBuilder)
    {
        $this->permissionBuilder = $permissionBuilder;
    }

    /**
     * Get all the user roles from the system.
     */
    public function getAllRoles(): Collection
    {
        return Role::query()->get();
    }

    /**
     * Get all the roles except for the provided one.
     */
    public function getAllRolesExcept(Role $role): Collection
    {
        return Role::query()->where('id', '!=', $role->id)->get();
    }

    /**
     * Get a role via its ID.
     */
    public function getRoleById(int $id): Role
    {
        return Role::query()->findOrFail($id);
    }

    /**
     * Save a new role into the system.
     */
    public function saveNewRole(array $roleData): Role
    {
        $role = new Role($roleData);
        $role->mfa_enforced = boolval($roleData['mfa_enforced'] ?? false);
        $role->save();

        $permissions = $roleData['permissions'] ?? [];
        $this->assignRolePermissions($role, $permissions);
        $this->permissionBuilder->rebuildForRole($role);

        Activity::add(ActivityType::ROLE_CREATE, $role);

        return $role;
    }

    /**
     * Updates an existing role.
     * Ensures Admin system role always have core permissions.
     */
    public function updateRole($roleId, array $roleData): Role
    {
        $role = $this->getRoleById($roleId);

        if (isset($roleData['permissions'])) {
            $this->assignRolePermissions($role, $roleData['permissions']);
        }

        $role->fill($roleData);
        $role->save();
        $this->permissionBuilder->rebuildForRole($role);

        Activity::add(ActivityType::ROLE_UPDATE, $role);

        return $role;
    }

    /**
     * Assign a list of permission names to the given role.
     */
    protected function assignRolePermissions(Role $role, array $permissionNameArray = []): void
    {
        $permissions = [];
        $permissionNameArray = array_values($permissionNameArray);

        // Ensure the admin system role retains vital system permissions
        if ($role->system_name === 'admin') {
            $permissionNameArray = array_unique(array_merge($permissionNameArray, [
                'users-manage',
                'user-roles-manage',
                'restrictions-manage-all',
                'restrictions-manage-own',
                'settings-manage',
            ]));
        }

        if (!empty($permissionNameArray)) {
            $permissions = RolePermission::query()
                ->whereIn('name', $permissionNameArray)
                ->pluck('id')
                ->toArray();
        }

        $role->permissions()->sync($permissions);
    }

    /**
     * Delete a role from the system.
     * Check it's not an admin role or set as default before deleting.
     * If a migration Role ID is specified the users assign to the current role
     * will be added to the role of the specified id.
     *
     * @throws PermissionsException
     * @throws Exception
     */
    public function deleteRole(int $roleId, int $migrateRoleId = 0): void
    {
        $role = $this->getRoleById($roleId);

        // Prevent deleting admin role or default registration role.
        if ($role->system_name && in_array($role->system_name, $this->systemRoles)) {
            throw new PermissionsException(trans('errors.role_system_cannot_be_deleted'));
        } elseif ($role->id === intval(setting('registration-role'))) {
            throw new PermissionsException(trans('errors.role_registration_default_cannot_delete'));
        }

        if ($migrateRoleId !== 0) {
            $newRole = Role::query()->find($migrateRoleId);
            if ($newRole) {
                $users = $role->users()->pluck('id')->toArray();
                $newRole->users()->sync($users);
            }
        }

        $role->entityPermissions()->delete();
        $role->jointPermissions()->delete();
        Activity::add(ActivityType::ROLE_DELETE, $role);
        $role->delete();
    }
}
