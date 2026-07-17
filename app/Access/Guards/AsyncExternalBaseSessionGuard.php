<?php

namespace BookStack\Access\Guards;

/**
 * External Auth Session Guard.
 *
 * The login process for external auth (SAML2/OIDC) is async in nature, meaning it does not fit very well
 * into the default laravel 'Guard' auth flow. Instead, most of the logic is done via the relevant
 * controller and services. This class provides a safer, thin version of SessionGuard.
 */
class AsyncExternalBaseSessionGuard extends ExternalBaseSessionGuard
{
    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param bool  $remember
     */
    public function attempt(array $credentials = [], $remember = false): bool
    {
        return false;
    }
}
