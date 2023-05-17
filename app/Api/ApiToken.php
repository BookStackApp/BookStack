<?php

namespace BookStack\Api;

use BookStack\Activity\Models\Loggable;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ApiToken.
 *
 * @property int    $id
 * @property string $token_id
 * @property string $secret
 * @property string $name
 * @property Carbon $expires_at
 * @property User   $user
 */
class ApiToken extends Model implements Loggable
{
    protected $fillable = ['name', 'expires_at'];
    protected $casts = [
        'expires_at' => 'date:Y-m-d',
    ];

    /**
     * Get the user that this token belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default expiry value for an API token.
     * Set to 100 years from now.
     */
    public static function defaultExpiry(): string
    {
        return Carbon::now()->addYears(100)->format('Y-m-d');
    }

    /**
     * {@inheritdoc}
     */
    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}; User: {$this->user->logDescriptor()}";
    }
}
