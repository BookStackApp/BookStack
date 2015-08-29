<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The roles that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('Oxbow\User');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany('Oxbow\Permission');
    }

    /**
     * Add a permission to this role.
     * @param Permission $permission
     */
    public function attachPermission(Permission $permission)
    {
        $this->permissions()->attach($permission->id);
    }

}
