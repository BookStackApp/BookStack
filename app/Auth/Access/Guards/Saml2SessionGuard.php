<?php

namespace BookStack\Auth\Access\Guards;

/**
 * Saml2 Session Guard
 *
 * The saml2 login process is async in nature meaning it does not fit very well
 * into the default laravel 'Guard' auth flow. Instead most of the logic is done
 * via the Saml2 controller & Saml2Service. This class provides a safer, thin
 * version of SessionGuard.
 */
class Saml2SessionGuard extends ExternalBaseSessionGuard
{
    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        return false;
    }

}
