<?php namespace BookStack;

use BookStack\Notifications\ResetPassword;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Notifiable;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name', 'email', 'image_id', 'books_view_type' ];

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
     * Returns the default public user.
     * @return User
     */
    public static function getDefault()
    {
        return static::where('system_name', '=', 'public')->first();
    }

    /**
     * Check if the user is the default public user.
     * @return bool
     */
    public function isDefault()
    {
        return $this->system_name === 'public';
    }

    /**
     * The roles that belong to the user.
     * @return BelongsToMany
     */
    public function roles()
    {
        if ($this->id === 0) return ;
        return $this->belongsToMany(Role::class);
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
     * Check if the user has a role.
     * @param $role
     * @return mixed
     */
    public function hasSystemRole($role)
    {
        return $this->roles->pluck('system_name')->contains('admin');
    }

    /**
     * Get all permissions belonging to a the current user.
     * @param bool $cache
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function permissions($cache = true)
    {
        if(isset($this->permissions) && $cache) return $this->permissions;
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
        $this->roles()->attach($id);
    }

    /**
     * Get the social account associated with this user.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
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
        $default = baseUrl('/user_avatar.png');
        $imageId = $this->image_id;
        if ($imageId === 0 || $imageId === '0' || $imageId === null) return $default;

        try {
            $avatar = $this->avatar ? baseUrl($this->avatar->getThumb($size, $size, false)) : $default;
        } catch (\Exception $err) {
            $avatar = $default;
        }
        return $avatar;
    }

    /**
     * Get the avatar for the user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    /**
     * Get the url for editing this user.
     * @return string
     */
    public function getEditUrl()
    {
        return baseUrl('/settings/users/' . $this->id);
    }

    /**
     * Get the url that links to this user's profile.
     * @return mixed
     */
    public function getProfileUrl()
    {
        return baseUrl('/user/' . $this->id);
    }

    /**
     * Get a shortened version of the user's name.
     * @param int $chars
     * @return string
     */
    public function getShortName($chars = 8)
    {
        if (strlen($this->name) <= $chars) return $this->name;

        $splitName = explode(' ', $this->name);
        if (strlen($splitName[0]) <= $chars) return $splitName[0];

        return '';
    }

    /**
     * Send the password reset notification.
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
