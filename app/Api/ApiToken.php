<?php namespace BookStack\Api;

use BookStack\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{
    protected $fillable = ['name', 'expires_at'];
    protected $casts = [
        'expires_at' => 'date:Y-m-d'
    ];

    /**
     * Get the user that this token belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
