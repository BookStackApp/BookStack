<?php

namespace BookStack\Auth\Access\Guards;

use BookStack\Auth\Access\OpenIdService;
use BookStack\Auth\Access\RegistrationService;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;

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

    protected $openidService;

    /**
     * OpenIdSessionGuard constructor.
     */
    public function __construct(
        $name,
        UserProvider $provider,
        Session $session,
        OpenIdService $openidService,
        RegistrationService $registrationService
    ) {
        $this->openidService = $openidService;
        parent::__construct($name, $provider, $session, $registrationService);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // retrieve the current user
        $user = parent::user();

        // refresh the current user
        if ($user && !$this->openidService->refresh()) {
            $this->user = null;
        }

        return $this->user;
    }

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
