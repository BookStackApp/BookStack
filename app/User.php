<?php

namespace Oxbow;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Returns a default guest user.
     */
    public static function getDefault()
    {
        return new static([
            'email' => 'guest',
            'name'  => 'Guest'
        ]);
    }

    /**
     * Permissions and roles
     */

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('Oxbow\Role');
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }

    /**
     * Check if the user has a particular permission.
     * @param $permissionName
     * @return bool
     */
    public function can($permissionName)
    {
        if($this->email == 'guest') {
            return false;
        }
        $permissions = $this->role->permissions()->get();
        $permissionSearch = $permissions->search(function ($item, $key) use ($permissionName) {
            return $item->name == $permissionName;
        });
        return $permissionSearch !== false;
    }

    /**
     * Attach a role to this user.
     * @param Role $role
     */
    public function attachRole(Role $role)
    {
        $this->attachRoleId($role->id);
    }

    /**
     * Attach a role id to this user.
     * @param $id
     */
    public function attachRoleId($id)
    {
        $this->roles()->sync([$id]);
    }

    /**
     * Returns the user's avatar,
     * Uses Gravatar as the avatar service.
     *
     * @param int $size
     * @return string
     */
    public function getAvatar($size = 50)
    {
        $emailHash = md5(strtolower(trim($this->email)));
        return '//www.gravatar.com/avatar/' . $emailHash . '?s=' . $size . '&d=identicon';
    }
}
