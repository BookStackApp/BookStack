<?php namespace BookStack\Auth;

use BookStack\Interfaces\Loggable;
use BookStack\Model;

/**
 * Class SocialAccount
 * @property string $driver
 * @property User $user
 * @package BookStack\Auth
 */
class SocialAccount extends Model implements Loggable
{

    protected $fillable = ['user_id', 'driver', 'driver_id', 'timestamps'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @inheritDoc
     */
    public function logDescriptor(): string
    {
        return "{$this->driver}; {$this->user->logDescriptor()}";
    }
}
