<?php namespace BookStack;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany('BookStack\Role');
    }

    /**
     * Get the permission object by name.
     * @param $name
     * @return mixed
     */
    public static function getByName($name)
    {
        return static::where('name', '=', $name)->first();
    }
}
