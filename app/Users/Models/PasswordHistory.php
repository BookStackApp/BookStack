<?php

namespace BookStack\Users\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use BookStack\App\Model;
use BookStack\Exceptions\PasswordHistoryException;


class PasswordHistory extends Model
{
    protected $fillable = [
        'user_id', 'hash'
    ];

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    /**
    * Creates a password history log upon password creation or change
    * @throws PasswordHistoryException
    */
    public static function create(array $newHistory)
    {

        if (env('PASSWORD_HISTORY', 0) < 1) return;

        $histories = PasswordHistory::where('user_id', $newHistory['user_id'])->orderBy('created_at', 'desc')->take(env('PASSWORD_HISTORY'))->get();
        $last_password_created_at = $histories->first()->created_at;

        if ($last_password_created_at->greaterThan(Carbon::now()->subDays(env('PASSWORD_MIN_AGE', 0)))){
            throw new PasswordHistoryException('Password has been recently changed');
        }

        foreach($histories as $history) {
            if (Hash::check($newHistory['password'], $history['hash'])){
                throw new PasswordHistoryException('Password has been recently used');
            }
        }

        return static::query()->create([
            'user_id' => $newHistory['user_id'],
            'hash' => Hash::make($newHistory['password']),
        ]);
    }

    /**
    * Creates a history for existing passwords, only used in initial migration
    */
    public static function create_initial_history(array $newHistory)
    {
        return static::query()->create([
            'user_id' => $newHistory['user_id'],
            'hash' => $newHistory['hash'],
        ]);
    }

}
