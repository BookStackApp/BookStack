<?php

namespace BookStack\Auth\Access\Guards;

use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Access\RegistrationService;
use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\LdapException;
use BookStack\Exceptions\LoginAttemptEmailNeededException;
use BookStack\Exceptions\LoginAttemptException;
use BookStack\Exceptions\UserRegistrationException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Str;

class LdapSessionGuard extends ExternalBaseSessionGuard
{
    protected LdapService $ldapService;

    /**
     * LdapSessionGuard constructor.
     */
    public function __construct(
        $name,
        UserProvider $provider,
        Session $session,
        LdapService $ldapService,
        RegistrationService $registrationService
    ) {
        $this->ldapService = $ldapService;
        parent::__construct($name, $provider, $session, $registrationService);
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @throws LdapException
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $userDetails = $this->ldapService->getUserDetails($credentials['username']);

        if (isset($userDetails['uid'])) {
            $this->lastAttempted = $this->provider->retrieveByCredentials([
                'external_auth_id' => $userDetails['uid'],
            ]);
        }

        return $this->ldapService->validateUserCredentials($userDetails, $credentials['password']);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @throws LdapException*@throws \BookStack\Exceptions\JsonDebugException
     * @throws LoginAttemptException
     * @throws JsonDebugException
     *
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $username = $credentials['username'];
        $userDetails = $this->ldapService->getUserDetails($username);

        $user = null;
        if (isset($userDetails['uid'])) {
            $this->lastAttempted = $user = $this->provider->retrieveByCredentials([
                'external_auth_id' => $userDetails['uid'],
            ]);
        }

        if (!$this->ldapService->validateUserCredentials($userDetails, $credentials['password'])) {
            return false;
        }

        if (is_null($user)) {
            try {
                $user = $this->createNewFromLdapAndCreds($userDetails, $credentials);
            } catch (UserRegistrationException $exception) {
                throw new LoginAttemptException($exception->getMessage());
            }
        }

        // Sync LDAP groups if required
        if ($this->ldapService->shouldSyncGroups()) {
            $this->ldapService->syncGroups($user, $username);
        }

        // Attach avatar if non-existent
        if (!$user->avatar()->exists()) {
            $this->ldapService->saveAndAttachAvatar($user, $userDetails);
        }

        $this->login($user, $remember);

        return true;
    }

    /**
     * Create a new user from the given ldap credentials and login credentials.
     *
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
            'name'             => $ldapUserDetails['name'],
            'email'            => $ldapUserDetails['email'] ?: $credentials['email'],
            'external_auth_id' => $ldapUserDetails['uid'],
            'password'         => Str::random(32),
        ];

        $user = $this->registrationService->registerUser($details, null, false);
        $this->ldapService->saveAndAttachAvatar($user, $ldapUserDetails);

        return $user;
    }
}
