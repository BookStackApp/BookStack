<?php

namespace BookStack\Auth\Access\Guards;

use BookStack\Auth\Access\LdapService;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\LdapException;
use BookStack\Exceptions\LoginAttemptException;
use BookStack\Exceptions\LoginAttemptEmailNeededException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;

class LdapSessionGuard extends ExternalBaseSessionGuard
{

    protected $ldapService;
    protected $userRepo;

    /**
     * LdapSessionGuard constructor.
     */
    public function __construct($name,
        UserProvider $provider,
        Session $session,
        LdapService $ldapService,
        UserRepo $userRepo
    )
    {
        $this->ldapService = $ldapService;
        $this->userRepo = $userRepo;
        parent::__construct($name, $provider, $session);
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
        $this->lastAttempted = $this->provider->retrieveByCredentials([
            'external_auth_id' => $userDetails['uid']
        ]);

        return $this->ldapService->validateUserCredentials($userDetails, $credentials['username'], $credentials['password']);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     * @throws LoginAttemptEmailNeededException
     * @throws LoginAttemptException
     * @throws LdapException
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $username = $credentials['username'];
        $userDetails = $this->ldapService->getUserDetails($username);
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials([
            'external_auth_id' => $userDetails['uid']
        ]);

        if (!$this->ldapService->validateUserCredentials($userDetails, $username, $credentials['password'])) {
            return false;
        }

        if (is_null($user)) {
            $user = $this->freshUserInstanceFromLdapUserDetails($userDetails);
        }

        $this->checkForUserEmail($user, $credentials['email'] ?? '');
        $this->saveIfNew($user);

        // Sync LDAP groups if required
        if ($this->ldapService->shouldSyncGroups()) {
            $this->ldapService->syncGroups($user, $username);
        }

        $this->login($user, $remember);
        return true;
    }

    /**
     * Save the give user if they don't yet existing in the system.
     * @throws LoginAttemptException
     */
    protected function saveIfNew(User $user)
    {
        if ($user->exists) {
            return;
        }

        // Check for existing users with same email
        $alreadyUser = $user->newQuery()->where('email', '=', $user->email)->count() > 0;
        if ($alreadyUser) {
            throw new LoginAttemptException(trans('errors.error_user_exists_different_creds', ['email' => $user->email]));
        }

        $user->save();
        $this->userRepo->attachDefaultRole($user);
        $this->userRepo->downloadAndAssignUserAvatar($user);
    }

    /**
     * Ensure the given user has an email.
     * Takes the provided email in the request if a value is provided
     * and the user does not have an existing email.
     * @throws LoginAttemptEmailNeededException
     */
    protected function checkForUserEmail(User $user, string $providedEmail)
    {
        // Request email if missing from user and missing from request
        if (is_null($user->email) && !$providedEmail) {
            throw new LoginAttemptEmailNeededException();
        }

        // Add email to model if non-existing and email provided in request
        if (!$user->exists && is_null($user->email) && $providedEmail) {
            $user->email = $providedEmail;
        }
    }

    /**
     * Create a fresh user instance from details provided by a LDAP lookup.
     */
    protected function freshUserInstanceFromLdapUserDetails(array $ldapUserDetails): User
    {
        $user = new User();

        $user->name = $ldapUserDetails['name'];
        $user->external_auth_id = $ldapUserDetails['uid'];
        $user->email = $ldapUserDetails['email'];
        $user->email_confirmed = false;

        return $user;
    }

}
