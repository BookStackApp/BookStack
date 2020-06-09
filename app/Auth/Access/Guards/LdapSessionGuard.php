<?php

namespace BookStack\Auth\Access\Guards;

use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Access\RegistrationService;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\LdapException;
use BookStack\Exceptions\LoginAttemptException;
use BookStack\Exceptions\LoginAttemptEmailNeededException;
use BookStack\Exceptions\UserRegistrationException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LdapSessionGuard extends ExternalBaseSessionGuard
{

    protected $ldapService;

    /**
     * LdapSessionGuard constructor.
     */
    public function __construct($name,
        UserProvider $provider,
        Session $session,
        LdapService $ldapService,
        RegistrationService $registrationService
    )
    {
        $this->ldapService = $ldapService;
        parent::__construct($name, $provider, $session, $registrationService);
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     * @throws LdapException
     */
    public function validate(array $credentials = [])
    {
        $userDetails = $this->ldapService->getUserDetails($credentials['username']);

        if (isset($userDetails['uid'])) {
            $this->lastAttempted = $this->provider->retrieveByCredentials([
                'external_auth_id' => $userDetails['uid']
            ]);
        }

        return $this->ldapService->validateUserCredentials($userDetails, $credentials['password']);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     * @throws LoginAttemptException
     * @throws LdapException
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $username = $credentials['username'];
        $userDetails = $this->ldapService->getUserDetails($username);

        $user = null;
        if (isset($userDetails['uid'])) {
            $this->lastAttempted = $user = $this->provider->retrieveByCredentials([
                'external_auth_id' => $userDetails['uid']
            ]);
        }

        if (!$this->ldapService->validateUserCredentials($userDetails, $credentials['password'])) {
            return false;
        }

        if (is_null($user)) {
            try {
                $user = $this->createNewFromLdapAndCreds($userDetails, $credentials);
            } catch (UserRegistrationException $exception) {
                throw new LoginAttemptException($exception->message);
            }
        }

        // Sync LDAP groups if required
        if ($this->ldapService->shouldSyncGroups()) {
            $this->ldapService->syncGroups($user, $username);
        }

        $this->login($user, $remember);
        return true;
    }

    /**
     * Create a new user from the given ldap credentials and login credentials
     * @throws LoginAttemptEmailNeededException
     * @throws LoginAttemptException
     * @throws UserRegistrationException
     */
    protected function createNewFromLdapAndCreds(array $ldapUserDetails, array $credentials): User
    {
        $email = trim($ldapUserDetails['email'] ?: ($credentials['email'] ?? ''));

        if (empty($email)) {
            throw new LoginAttemptEmailNeededException();
        }

        $details = [
            'name' => $ldapUserDetails['name'],
            'email' => $ldapUserDetails['email'] ?: $credentials['email'],
            'external_auth_id' => $ldapUserDetails['uid'],
            'password' => Str::random(32),
        ];

        return $this->registrationService->registerUser($details, null, false);
    }

}
