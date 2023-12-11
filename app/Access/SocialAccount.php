<?php

namespace BookStack\Access;

use BookStack\Activity\Models\Loggable;
use BookStack\App\Model;
use BookStack\Users\Models\User;

/**
 * Class SocialAccount.
 *
 * @property string $driver
 * @property User   $user
 */
class SocialAccount extends Model implements Loggable
{
    protected $fillable = ['user_id', 'driver', 'driver_id', 'timestamps'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * {@inheritdoc}
     */
    public function logDescriptor(): string
    {
        return "{$this->driver}; {$this->user->logDescriptor()}";
    }
}
