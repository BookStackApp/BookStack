<?php

namespace BookStack;

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
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name', 'email', 'image_id'];

    /**
     * The attributes excluded from the model's JSON form.
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * This holds the user's permissions when loaded.
     * @var array
     */
    protected $permissions;

    /**
     * Returns a default guest user.
     */
    public static function getDefault()
    {
        return new static([
            'email' => 'guest',
            'name' => 'Guest'
        ]);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('BookStack\Role');
    }

    /**
     * Check if the user has a role.
     * @param $role
     * @return mixed
     */
    public function hasRole($role)
    {
        return $this->roles->pluck('name')->contains($role);
    }

    /**
     * Get all permissions belonging to a the current user.
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function permissions()
    {
        if(isset($this->permissions)) return $this->permissions;
        $this->load('roles.permissions');
        $permissions = $this->roles->map(function($role) {
            return $role->permissions;
        })->flatten()->unique();
        $this->permissions = $permissions;
        return $permissions;
    }

    /**
     * Check if the user has a particular permission.
     * @param $permissionName
     * @return bool
     */
    public function can($permissionName)
    {
        if ($this->email === 'guest') return false;
        return $this->permissions()->pluck('name')->contains($permissionName);
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
     * Get the social account associated with this user.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialAccounts()
    {
        return $this->hasMany('BookStack\SocialAccount');
    }

    /**
     * Check if the user has a social account,
     * If a driver is passed it checks for that single account type.
     * @param bool|string $socialDriver
     * @return bool
     */
    public function hasSocialAccount($socialDriver = false)
    {
        if ($socialDriver === false) {
            return $this->socialAccounts()->count() > 0;
        }

        return $this->socialAccounts()->where('driver', '=', $socialDriver)->exists();
    }

    /**
     * Returns the user's avatar,
     * @param int $size
     * @return string
     */
    public function getAvatar($size = 50)
    {
        if ($this->image_id === 0 || $this->image_id === '0' || $this->image_id === null) return '/user_avatar.png';
        return $this->avatar->getThumb($size, $size, false);
    }

    /**
     * Get the avatar for the user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo('BookStack\Image', 'image_id');
    }

    /**
     * Get the url for editing this user.
     * @return string
     */
    public function getEditUrl()
    {
        return '/settings/users/' . $this->id;
    }
}
