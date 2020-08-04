<?php namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Exceptions\PermissionsException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PermissionsRepo
{

    protected $permission;
    protected $role;
    protected $permissionService;

    protected $systemRoles = ['admin', 'public'];

    /**
     * PermissionsRepo constructor.
     */
    public function __construct(RolePermission $permission, Role $role, PermissionService $permissionService)
    {
        $this->permission = $permission;
        $this->role = $role;
        $this->permissionService = $permissionService;
    }

    /**
     * Get all the user roles from the system.
     */
    public function getAllRoles(): Collection
    {
        return $this->role->all();
    }

    /**
     * Get all the roles except for the provided one.
     */
    public function getAllRolesExcept(Role $role): Collection
    {
        return $this->role->where('id', '!=', $role->id)->get();
    }

    /**
     * Get a role via its ID.
     */
    public function getRoleById($id): Role
    {
        return $this->role->newQuery()->findOrFail($id);
    }

    /**
     * Save a new role into the system.
     */
    public function saveNewRole(array $roleData): Role
    {
        $role = $this->role->newInstance($roleData);
        $role->save();

        $permissions = isset($roleData['permissions']) ? array_keys($roleData['permissions']) : [];
        $this->assignRolePermissions($role, $permissions);
        $this->permissionService->buildJointPermissionForRole($role);
        return $role;
    }

    /**
     * Updates an existing role.
     * Ensure Admin role always have core permissions.
     */
    public function updateRole($roleId, array $roleData)
    {
        /** @var Role $role */
        $role = $this->role->newQuery()->findOrFail($roleId);

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
        $role->save();
        $this->permissionService->buildJointPermissionForRole($role);
    }

    /**
     * Assign an list of permission names to an role.
     */
    public function assignRolePermissions(Role $role, array $permissionNameArray = [])
    {
        $permissions = [];
        $permissionNameArray = array_values($permissionNameArray);

        if ($permissionNameArray) {
            $permissions = $this->permission->newQuery()
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
     * @throws PermissionsException
     * @throws Exception
     */
    public function deleteRole($roleId, $migrateRoleId)
    {
        /** @var Role $role */
        $role = $this->role->newQuery()->findOrFail($roleId);

        // Prevent deleting admin role or default registration role.
        if ($role->system_name && in_array($role->system_name, $this->systemRoles)) {
            throw new PermissionsException(trans('errors.role_system_cannot_be_deleted'));
        } else if ($role->id === intval(setting('registration-role'))) {
            throw new PermissionsException(trans('errors.role_registration_default_cannot_delete'));
        }

        if ($migrateRoleId) {
            $newRole = $this->role->newQuery()->find($migrateRoleId);
            if ($newRole) {
                $users = $role->users()->pluck('id')->toArray();
                $newRole->users()->sync($users);
            }
        }

        $this->permissionService->deleteJointPermissionsForRole($role);
        $role->delete();
    }
}
