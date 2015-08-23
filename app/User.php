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
    protected $fillable = ['name', 'email'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Returns the user's avatar,
     * Uses Gravatar as the avatar service.
     * @param int $size
     * @return string
     */
    public function getAvatar($size = 50)
    {
        $emailHash = md5(strtolower(trim($this->email)));
        return '//www.gravatar.com/avatar/' . $emailHash . '?s=' . $size . '&d=identicon';
    }
}
