<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = ['display_name', 'description'];

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
     * Check if this role has a permission.
     * @param $permission
     */
    public function hasPermission($permission)
    {
        return $this->permissions->pluck('name')->contains($permission);
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
     * Get the role object for the specified role.
     * @param $roleName
     * @return mixed
     */
    public static function getRole($roleName)
    {
        return static::where('name', '=', $roleName)->first();
    }
}
