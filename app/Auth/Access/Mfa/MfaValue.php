<?php

namespace BookStack\Auth\Access\Mfa;

use BookStack\Auth\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $method
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MfaValue extends Model
{
    protected static $unguarded = true;

    const METHOD_TOTP = 'totp';
    const METHOD_BACKUP_CODES = 'backup_codes';

    /**
     * Get all the MFA methods available.
     */
    public static function allMethods(): array
    {
        return [self::METHOD_TOTP, self::METHOD_BACKUP_CODES];
    }

    /**
     * Upsert a new MFA value for the given user and method
     * using the provided value.
     */
    public static function upsertWithValue(User $user, string $method, string $value): void
    {
        /** @var MfaValue $mfaVal */
        $mfaVal = static::query()->firstOrNew([
            'user_id' => $user->id,
            'method' => $method
        ]);
        $mfaVal->setValue($value);
        $mfaVal->save();
    }

    /**
     * Easily get the decrypted MFA value for the given user and method.
     */
    public static function getValueForUser(User $user, string $method): ?string
    {
        /** @var MfaValue $mfaVal */
        $mfaVal = static::query()
            ->where('user_id', '=', $user->id)
            ->where('method', '=', $method)
            ->first();

        return $mfaVal ? $mfaVal->getValue() : null;
    }

    /**
     * Delete any stored MFA values for the given user and method.
     */
    public static function deleteValuesForUser(User $user, string $method): void
    {
        static::query()
            ->where('user_id', '=', $user->id)
            ->where('method', '=', $method)
            ->delete();
    }

    /**
     * Decrypt the value attribute upon access.
     */
    protected function getValue(): string
    {
        return decrypt($this->value);
    }

    /**
     * Encrypt the value attribute upon access.
     */
    protected function setValue($value): void
    {
        $this->value = encrypt($value);
    }
}
