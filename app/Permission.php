<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany('Oxbow\Permissions');
    }
}
