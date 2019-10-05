<?php namespace BookStack\Auth;

use BookStack\Auth\Permissions\JointPermission;
use BookStack\Auth\Permissions\RolePermission;
use BookStack\Model;

class Role extends Model
{

    protected $fillable = ['display_name', 'description', 'external_auth_id'];

    /**
     * The roles that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->orderBy('name', 'asc');
    }

    /**
     * Get all related JointPermissions.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jointPermissions()
    {
        return $this->hasMany(JointPermission::class);
    }

    /**
     * The RolePermissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(RolePermission::class, 'permission_role', 'role_id', 'permission_id');
    }

    /**
     * Check if this role has a permission.
     * @param $permissionName
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        $permissions = $this->getRelationValue('permissions');
        foreach ($permissions as $permission) {
            if ($permission->getRawAttribute('name') === $permissionName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add a permission to this role.
     * @param RolePermission $permission
     */
    public function attachPermission(RolePermission $permission)
    {
        $this->permissions()->attach($permission->id);
    }

    /**
     * Detach a single permission from this role.
     * @param RolePermission $permission
     */
    public function detachPermission(RolePermission $permission)
    {
        $this->permissions()->detach($permission->id);
    }

    /**
     * Get the role object for the specified role.
     * @param $roleName
     * @return Role
     */
    public static function getRole($roleName)
    {
        return static::query()->where('name', '=', $roleName)->first();
    }

    /**
     * Get the role object for the specified system role.
     * @param $roleName
     * @return Role
     */
    public static function getSystemRole($roleName)
    {
        return static::query()->where('system_name', '=', $roleName)->first();
    }

    /**
     * Get all visible roles
     * @return mixed
     */
    public static function visible()
    {
        return static::query()->where('hidden', '=', false)->orderBy('name')->get();
    }

    /**
     * Get the roles that can be restricted.
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function restrictable()
    {
        return static::query()->where('system_name', '!=', 'admin')->get();
    }
}
