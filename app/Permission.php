<?php

namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany('BookStack\Permissions');
    }
}
