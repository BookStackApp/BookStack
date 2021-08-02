<?php

namespace BookStack\Auth\Access;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\Mfa\MfaSession;
use BookStack\Auth\User;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Facades\Activity;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Method;

class LoginService
{

    protected const LAST_LOGIN_ATTEMPTED_SESSION_KEY = 'auth-login-last-attempted';

    protected $mfaSession;

    public function __construct(MfaSession $mfaSession)
    {
        $this->mfaSession = $mfaSession;
    }

    /**
     * Log the given user into the system.
     * Will start a login of the given user but will prevent if there's
     * a reason to (MFA or Unconfirmed Email).
     * Returns a boolean to indicate the current login result.
     * @throws StoppedAuthenticationException
     */
    public function login(User $user, string $method): void
    {
        if ($this->awaitingEmailConfirmation($user) || $this->needsMfaVerification($user)) {
            $this->setLastLoginAttemptedForUser($user, $method);
            throw new StoppedAuthenticationException($user, $this);
            // TODO - Does 'remember' still work? Probably not right now.

            // TODO - Need to clear MFA sessions out upon logout

            // Old MFA middleware todos:

            // TODO - Handle email confirmation handling
            //  Left BookStack\Http\Middleware\Authenticate@emailConfirmationErrorResponse in which needs
            //  be removed as an example of old behaviour.
        }

        $this->clearLastLoginAttempted();
        auth()->login($user);
        Activity::add(ActivityType::AUTH_LOGIN, "{$method}; {$user->logDescriptor()}");
        Theme::dispatch(ThemeEvents::AUTH_LOGIN, $method, $user);

        // Authenticate on all session guards if a likely admin
        if ($user->can('users-manage') && $user->can('user-roles-manage')) {
            $guards = ['standard', 'ldap', 'saml2'];
            foreach ($guards as $guard) {
                auth($guard)->login($user);
            }
        }
    }

    /**
     * Reattempt a system login after a previous stopped attempt.
     * @throws Exception
     */
    public function reattemptLoginFor(User $user)
    {
        if ($user->id !== ($this->getLastLoginAttemptUser()->id ?? null)) {
            throw new Exception('Login reattempt user does align with current session state');
        }

        $this->login($user, $this->getLastLoginAttemptMethod());
    }

    /**
     * Get the last user that was attempted to be logged in.
     * Only exists if the last login attempt had correct credentials
     * but had been prevented by a secondary factor.
     */
    public function getLastLoginAttemptUser(): ?User
    {
        $id = $this->getLastLoginAttemptDetails()['user_id'];
        return User::query()->where('id', '=', $id)->first();
    }

    /**
     * Get the method for the last login attempt.
     */
    protected function getLastLoginAttemptMethod(): ?string
    {
        return $this->getLastLoginAttemptDetails()['method'];
    }

    /**
     * Get the details of the last login attempt.
     * Checks upon a ttl of about 1 hour since that last attempted login.
     * @return array{user_id: ?string, method: ?string}
     */
    protected function getLastLoginAttemptDetails(): array
    {
        $value = session()->get(self::LAST_LOGIN_ATTEMPTED_SESSION_KEY);
        if (!$value) {
            return ['user_id' => null, 'method' => null];
        }

        [$id, $method, $time] = explode(':', $value);
        $hourAgo = time() - (60*60);
        if ($time < $hourAgo) {
            $this->clearLastLoginAttempted();
            return ['user_id' => null, 'method' => null];
        }

        return ['user_id' => $id, 'method' => $method];
    }

    /**
     * Set the last login attempted user.
     * Must be only used when credentials are correct and a login could be
     * achieved but a secondary factor has stopped the login.
     */
    protected function setLastLoginAttemptedForUser(User $user, string $method)
    {
        session()->put(
            self::LAST_LOGIN_ATTEMPTED_SESSION_KEY,
            implode(':', [$user->id, $method, time()])
        );
    }

    /**
     * Clear the last login attempted session value.
     */
    protected function clearLastLoginAttempted(): void
    {
        session()->remove(self::LAST_LOGIN_ATTEMPTED_SESSION_KEY);
    }

    /**
     * Check if MFA verification is needed.
     */
    public function needsMfaVerification(User $user): bool
    {
        return !$this->mfaSession->isVerifiedForUser($user) && $this->mfaSession->isRequiredForUser($user);
    }

    /**
     * Check if the given user is awaiting email confirmation.
     */
    public function awaitingEmailConfirmation(User $user): bool
    {
        $requireConfirmation = (setting('registration-confirmation') || setting('registration-restrict'));
        return $requireConfirmation && !$user->email_confirmed;
    }

    /**
     * Attempt the login of a user using the given credentials.
     * Meant to mirror Laravel's default guard 'attempt' method
     * but in a manner that always routes through our login system.
     * May interrupt the flow if extra authentication requirements are imposed.
     *
     * @throws StoppedAuthenticationException
     */
    public function attempt(array $credentials, string $method, bool $remember = false): bool
    {
        $result = auth()->attempt($credentials, $remember);
        if ($result) {
            $user = auth()->user();
            auth()->logout();
            $this->login($user, $method);
        }

        return $result;
    }

}