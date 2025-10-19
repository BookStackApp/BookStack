<?php

namespace BookStack\Access;

use BookStack\Activity\Models\Loggable;
use BookStack\App\Model;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $driver
 * @property User   $user
 */
class SocialAccount extends Model implements Loggable
{
    use HasFactory;

    protected $fillable = ['user_id', 'driver', 'driver_id'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
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
