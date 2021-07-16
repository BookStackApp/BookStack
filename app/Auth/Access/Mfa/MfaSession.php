<?php

namespace BookStack\Auth\Access\Mfa;

class MfaSession
{
    private const MFA_VERIFIED_SESSION_KEY = 'mfa-verification-passed';

    /**
     * Check if MFA is required for the current user.
     */
    public function requiredForCurrentUser(): bool
    {
        // TODO - Test both these cases
        return user()->mfaValues()->exists() || $this->currentUserRoleEnforcesMfa();
    }

    /**
     * Check if a role of the current user enforces MFA.
     */
    protected function currentUserRoleEnforcesMfa(): bool
    {
        return user()->roles()
            ->where('mfa_enforced', '=', true)
            ->exists();
    }

    /**
     * Check if the current MFA session has already been verified.
     */
    public function isVerified(): bool
    {
        return session()->get(self::MFA_VERIFIED_SESSION_KEY) === 'true';
    }

    /**
     * Mark the current session as MFA-verified.
     */
    public function markVerified(): void
    {
        session()->put(self::MFA_VERIFIED_SESSION_KEY, 'true');
    }

}