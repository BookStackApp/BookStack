<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Sets the default role name for newly registed users.
     * @var string
     */
    protected static $default = 'viewer';

    /**
     * The roles that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('BookStack\User');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany('BookStack\Permission');
    }

    /**
     * Add a permission to this role.
     * @param Permission $permission
     */
    public function attachPermission(Permission $permission)
    {
        $this->permissions()->attach($permission->id);
    }

    /**
     * Get an instance of the default role.
     * @return Role
     */
    public static function getDefault()
    {
        return static::getRole(static::$default);
    }

    /**
     * Get the role object for the specified role.
     * @param $roleName
     * @return mixed
     */
    public static function getRole($roleName)
    {
        return static::where('name', '=', $roleName)->first();
    }
}
