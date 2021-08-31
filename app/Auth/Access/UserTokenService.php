<?php

namespace BookStack\Auth\Access;

use BookStack\Auth\User;
use BookStack\Exceptions\UserTokenExpiredException;
use BookStack\Exceptions\UserTokenNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class UserTokenService
{
    /**
     * Name of table where user tokens are stored.
     *
     * @var string
     */
    protected $tokenTable = 'user_tokens';

    /**
     * Token expiry time in hours.
     *
     * @var int
     */
    protected $expiryTime = 24;

    /**
     * Delete all email confirmations that belong to a user.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function deleteByUser(User $user)
    {
        return DB::table($this->tokenTable)
            ->where('user_id', '=', $user->id)
            ->delete();
    }

    /**
     * Get the user id from a token, while check the token exists and has not expired.
     *
     * @param string $token
     *
     * @throws UserTokenNotFoundException
     * @throws UserTokenExpiredException
     *
     * @return int
     */
    public function checkTokenAndGetUserId(string $token): int
    {
        $entry = $this->getEntryByToken($token);

        if (is_null($entry)) {
            throw new UserTokenNotFoundException('Token "' . $token . '" not found');
        }

        if ($this->entryExpired($entry)) {
            throw new UserTokenExpiredException("Token of id {$entry->id} has expired.", $entry->user_id);
        }

        return $entry->user_id;
    }

    /**
     * Creates a unique token within the email confirmation database.
     *
     * @return string
     */
    protected function generateToken(): string
    {
        $token = Str::random(24);
        while ($this->tokenExists($token)) {
            $token = Str::random(25);
        }

        return $token;
    }

    /**
     * Generate and store a token for the given user.
     *
     * @param User $user
     *
     * @return string
     */
    protected function createTokenForUser(User $user): string
    {
        $token = $this->generateToken();
        DB::table($this->tokenTable)->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $token;
    }

    /**
     * Check if the given token exists.
     *
     * @param string $token
     *
     * @return bool
     */
    protected function tokenExists(string $token): bool
    {
        return DB::table($this->tokenTable)
            ->where('token', '=', $token)->exists();
    }

    /**
     * Get a token entry for the given token.
     *
     * @param string $token
     *
     * @return object|null
     */
    protected function getEntryByToken(string $token)
    {
        return DB::table($this->tokenTable)
            ->where('token', '=', $token)
            ->first();
    }

    /**
     * Check if the given token entry has expired.
     *
     * @param stdClass $tokenEntry
     *
     * @return bool
     */
    protected function entryExpired(stdClass $tokenEntry): bool
    {
        return Carbon::now()->subHours($this->expiryTime)
            ->gt(new Carbon($tokenEntry->created_at));
    }
}
