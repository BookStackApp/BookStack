<?php

namespace BookStack\Access\Guards;

use BookStack\Access\RegistrationService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;

/**
 * Class BaseSessionGuard
 * A base implementation of a session guard. Is a copy of the default Laravel
 * guard with 'remember' functionality removed. Basic auth and event emission
 * has also been removed to keep this simple. Designed to be extended by external
 * Auth Guards.
 */
class ExternalBaseSessionGuard implements StatefulGuard
{
    use GuardHelpers;

    /**
     * The name of the Guard. Typically "session".
     *
     * Corresponds to guard name in authentication configuration.
     */
    protected readonly string $name;

    /**
     * The user we last attempted to retrieve.
     */
    protected Authenticatable|null $lastAttempted;

    /**
     * The session used by the guard.
     */
    protected Session $session;

    /**
     * Indicates if the logout method has been called.
     */
    protected bool $loggedOut = false;

    /**
     * Service to handle common registration actions.
     */
    protected RegistrationService $registrationService;

    /**
     * Create a new authentication guard.
     */
    public function __construct(string $name, UserProvider $provider, Session $session, RegistrationService $registrationService)
    {
        $this->name = $name;
        $this->session = $session;
        $this->provider = $provider;
        $this->registrationService = $registrationService;
    }

    /**
     * Get the currently authenticated user.
     */
    public function user(): Authenticatable|null
    {
        if ($this->loggedOut) {
            return null;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());

        // First we will try to load the user using the
        // identifier in the session if one exists.
        if (!is_null($id)) {
            $this->user = $this->provider->retrieveById($id);
        }

        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id(): int|null
    {
        if ($this->loggedOut) {
            return null;
        }

        return $this->user()
            ? $this->user()->getAuthIdentifier()
            : $this->session->get($this->getName());
    }

    /**
     * Log a user into the application without sessions or cookies.
     */
    public function once(array $credentials = []): bool
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     */
    public function onceUsingId($id): Authenticatable|false
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            $this->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     * @param bool $remember
     */
    public function attempt(array $credentials = [], $remember = false): bool
    {
        return false;
    }

    /**
     * Log the given user ID into the application.
     * @param bool  $remember
     */
    public function loginUsingId(mixed $id, $remember = false): Authenticatable|false
    {
        // Always return false as to disable this method,
        // Logins should route through LoginService.
        return false;
    }

    /**
     * Log a user into the application.
     *
     * @param bool $remember
     */
    public function login(Authenticatable $user, $remember = false): void
    {
        $this->updateSession($user->getAuthIdentifier());

        $this->setUser($user);
    }

    /**
     * Update the session with the given ID.
     */
    protected function updateSession(string|int $id): void
    {
        $this->session->put($this->getName(), $id);

        $this->session->migrate(true);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(): void
    {
        $this->clearUserDataFromStorage();

        // Now we will clear the users out of memory so they are no longer available
        // as the user is no longer considered as being signed into this
        // application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Remove the user data from the session and cookies.
     */
    protected function clearUserDataFromStorage(): void
    {
        $this->session->remove($this->getName());
    }

    /**
     * Get the last user we attempted to authenticate.
     */
    public function getLastAttempted(): Authenticatable
    {
        return $this->lastAttempted;
    }

    /**
     * Get a unique identifier for the auth session value.
     */
    public function getName(): string
    {
        return 'login_' . $this->name . '_' . sha1(static::class);
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     */
    public function viaRemember(): bool
    {
        return false;
    }

    /**
     * Return the currently cached user.
     */
    public function getUser(): Authenticatable|null
    {
        return $this->user;
    }

    /**
     * Set the current user.
     */
    public function setUser(Authenticatable $user): self
    {
        $this->user = $user;

        $this->loggedOut = false;

        return $this;
    }
}
