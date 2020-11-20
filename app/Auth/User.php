<?php namespace BookStack\Auth;

use BookStack\Api\ApiToken;
use BookStack\Interfaces\Loggable;
use BookStack\Model;
use BookStack\Notifications\ResetPassword;
use BookStack\Uploads\Image;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @package BookStack\Auth
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $email_confirmed
 * @property int $image_id
 * @property string $external_auth_id
 * @property string $system_name
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, Loggable
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
    protected $fillable = ['name', 'email'];

    /**
     * The attributes excluded from the model's JSON form.
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'system_name', 'email_confirmed', 'external_auth_id', 'email',
        'created_at', 'updated_at', 'image_id',
    ];

    /**
     * This holds the user's permissions when loaded.
     * @var array
     */
    protected $permissions;

    /**
     * This holds the default user when loaded.
     * @var null|User
     */
    protected static $defaultUser = null;

    /**
     * Returns the default public user.
     * @return User
     */
    public static function getDefault()
    {
        if (!is_null(static::$defaultUser)) {
            return static::$defaultUser;
        }
        
        static::$defaultUser = static::where('system_name', '=', 'public')->first();
        return static::$defaultUser;
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
        if ($this->id === 0) {
            return ;
        }
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a role.
     */
    public function hasRole($roleId): bool
    {
        return $this->roles->pluck('id')->contains($roleId);
    }

    /**
     * Check if the user has a role.
     * @param $role
     * @return mixed
     */
    public function hasSystemRole($role)
    {
        return $this->roles->pluck('system_name')->contains($role);
    }

    /**
     * Attach the default system role to this user.
     */
    public function attachDefaultRole(): void
    {
        $roleId = setting('registration-role');
        if ($roleId && $this->roles()->where('id', '=', $roleId)->count() === 0) {
            $this->roles()->attach($roleId);
        }
    }

    /**
     * Get all permissions belonging to a the current user.
     * @param bool $cache
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function permissions($cache = true)
    {
        if (isset($this->permissions) && $cache) {
            return $this->permissions;
        }
        $this->load('roles.permissions');
        $permissions = $this->roles->map(function ($role) {
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
        if ($this->email === 'guest') {
            return false;
        }
        return $this->permissions()->pluck('name')->contains($permissionName);
    }

    /**
     * Attach a role to this user.
     */
    public function attachRole(Role $role)
    {
        $this->roles()->attach($role->id);
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
        $default = url('/user_avatar.png');
        $imageId = $this->image_id;
        if ($imageId === 0 || $imageId === '0' || $imageId === null) {
            return $default;
        }

        try {
            $avatar = $this->avatar ? url($this->avatar->getThumb($size, $size, false)) : $default;
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
     * Get the API tokens assigned to this user.
     */
    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     * Get the url for editing this user.
     */
    public function getEditUrl(string $path = ''): string
    {
        $uri = '/settings/users/' . $this->id . '/' . trim($path, '/');
        return url(rtrim($uri, '/'));
    }

    /**
     * Get the url that links to this user's profile.
     */
    public function getProfileUrl(): string
    {
        return url('/user/' . $this->id);
    }

    /**
     * Get a shortened version of the user's name.
     * @param int $chars
     * @return string
     */
    public function getShortName($chars = 8)
    {
        if (mb_strlen($this->name) <= $chars) {
            return $this->name;
        }

        $splitName = explode(' ', $this->name);
        if (mb_strlen($splitName[0]) <= $chars) {
            return $splitName[0];
        }

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

    /**
     * @inheritdoc
     */
    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}";
    }
}
