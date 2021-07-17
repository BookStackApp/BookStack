<?php

namespace BookStack\Auth\Access\Mfa;

use BookStack\Auth\User;

class MfaSession
{
    /**
     * Check if MFA is required for the given user.
     */
    public function isRequiredForUser(User $user): bool
    {
        // TODO - Test both these cases
        return $user->mfaValues()->exists() || $this->userRoleEnforcesMfa($user);
    }

    /**
     * Check if a role of the given user enforces MFA.
     */
    protected function userRoleEnforcesMfa(User $user): bool
    {
        return $user->roles()
            ->where('mfa_enforced', '=', true)
            ->exists();
    }

    /**
     * Check if the current MFA session has already been verified for the given user.
     */
    public function isVerifiedForUser(User $user): bool
    {
        return session()->get($this->getMfaVerifiedSessionKey($user)) === 'true';
    }

    /**
     * Mark the current session as MFA-verified.
     */
    public function markVerifiedForUser(User $user): void
    {
        session()->put($this->getMfaVerifiedSessionKey($user), 'true');
    }

    /**
     * Get the session key in which the MFA verification status is stored.
     */
    protected function getMfaVerifiedSessionKey(User $user): string
    {
        return 'mfa-verification-passed:' . $user->id;
    }

}