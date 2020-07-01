<?php

namespace BookStack\Auth\Access\Guards;

/**
 * OpenId Session Guard
 *
 * The OpenId login process is async in nature meaning it does not fit very well
 * into the default laravel 'Guard' auth flow. Instead most of the logic is done
 * via the OpenId controller & OpenIdService. This class provides a safer, thin
 * version of SessionGuard.
 *
 * @package BookStack\Auth\Access\Guards
 */
class OpenIdSessionGuard extends ExternalBaseSessionGuard
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
