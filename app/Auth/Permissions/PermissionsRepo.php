<?php

namespace BookStack\Auth\Permissions;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Role;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PermissionsRepo
{
    protected JointPermissionBuilder $permissionBuilder;
    protected $systemRoles = ['admin', 'public'];

    /**
     * PermissionsRepo constructor.
     */
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
    public function getRoleById($id): Role
    {
        return Role::query()->findOrFail($id);
    }

    /**
     * Save a new role into the system.
     */
    public function saveNewRole(array $roleData): Role
    {
        $role = new Role($roleData);
        $role->mfa_enforced = ($roleData['mfa_enforced'] ?? 'false') === 'true';
        $role->save();

        $permissions = isset($roleData['permissions']) ? array_keys($roleData['permissions']) : [];
        $this->assignRolePermissions($role, $permissions);
        $this->permissionBuilder->rebuildForRole($role);

        Activity::add(ActivityType::ROLE_CREATE, $role);

        return $role;
    }

    /**
     * Updates an existing role.
     * Ensure Admin role always have core permissions.
     */
    public function updateRole($roleId, array $roleData)
    {
        $role = $this->getRoleById($roleId);

        $permissions = isset($roleData['permissions']) ? array_keys($roleData['permissions']) : [];
        if ($role->system_name === 'admin') {
            $permissions = array_merge($permissions, [
                'users-manage',
                'user-roles-manage',
                'restrictions-manage-all',
                'restrictions-manage-own',
                'settings-manage',
            ]);
        }

        $this->assignRolePermissions($role, $permissions);

        $role->fill($roleData);
        $role->mfa_enforced = ($roleData['mfa_enforced'] ?? 'false') === 'true';
        $role->save();
        $this->permissionBuilder->rebuildForRole($role);

        Activity::add(ActivityType::ROLE_UPDATE, $role);
    }

    /**
     * Assign a list of permission names to a role.
     */
    protected function assignRolePermissions(Role $role, array $permissionNameArray = [])
    {
        $permissions = [];
        $permissionNameArray = array_values($permissionNameArray);

        if ($permissionNameArray) {
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
     * If an migration Role ID is specified the users assign to the current role
     * will be added to the role of the specified id.
     *
     * @throws PermissionsException
     * @throws Exception
     */
    public function deleteRole($roleId, $migrateRoleId)
    {
        $role = $this->getRoleById($roleId);

        // Prevent deleting admin role or default registration role.
        if ($role->system_name && in_array($role->system_name, $this->systemRoles)) {
            throw new PermissionsException(trans('errors.role_system_cannot_be_deleted'));
        } elseif ($role->id === intval(setting('registration-role'))) {
            throw new PermissionsException(trans('errors.role_registration_default_cannot_delete'));
        }

        if ($migrateRoleId) {
            $newRole = Role::query()->find($migrateRoleId);
            if ($newRole) {
                $users = $role->users()->pluck('id')->toArray();
                $newRole->users()->sync($users);
            }
        }

        $role->jointPermissions()->delete();
        Activity::add(ActivityType::ROLE_DELETE, $role);
        $role->delete();
    }
}
