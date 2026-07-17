<?php

namespace BookStack\Access;

use BookStack\Access\Notifications\UserInviteNotification;
use BookStack\Users\Models\User;

class UserInviteService extends UserTokenService
{
    protected string $tokenTable = 'user_invites';
    protected int $expiryTime = 336; // Two weeks

    /**
     * Send an invitation to a user to sign into BookStack
     * Removes existing invitation tokens.
     * @throws UserInviteException
     */
    public function sendInvitation(User $user)
    {
        $this->deleteByUser($user);
        $token = $this->createTokenForUser($user);

        try {
            $user->notify(new UserInviteNotification($token));
        } catch (\Exception $exception) {
            throw new UserInviteException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
