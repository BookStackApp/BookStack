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
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'image_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
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
     * Permissions and roles
     */

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('BookStack\Role');
    }

    public function getRoleAttribute()
    {
        return $this->roles()->with('permissions')->first();
    }

    /**
     * Loads the user's permissions from their role.
     */
    private function loadPermissions()
    {
        if (isset($this->permissions)) return;
        $this->load('roles.permissions');
        $permissions = $this->roles[0]->permissions;
        $permissionsArray = $permissions->pluck('name')->all();
        $this->permissions = $permissionsArray;
    }

    /**
     * Check if the user has a particular permission.
     * @param $permissionName
     * @return bool
     */
    public function can($permissionName)
    {
        if ($this->email == 'guest') {
            return false;
        }
        $this->loadPermissions();
        return array_search($permissionName, $this->permissions) !== false;
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
     *
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
     * Uses Gravatar as the avatar service.
     *
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
        return '/users/' . $this->id;
    }
}
