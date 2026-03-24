<?php

namespace BookStack\Access;

use BookStack\Access\Mfa\MfaSession;
use BookStack\Activity\ActivityType;
use BookStack\Exceptions\LoginAttemptException;
use BookStack\Exceptions\LoginAttemptInvalidUserException;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Facades\Activity;
use BookStack\Facades\Theme;
use BookStack\Permissions\Permission;
use BookStack\Theming\ThemeEvents;
use BookStack\Users\Models\User;
use Exception;

class LoginService
{
    protected const LAST_LOGIN_ATTEMPTED_SESSION_KEY = 'auth-login-last-attempted';
    protected const SESSION_METHOD_KEY = 'auth-login-method';

    public function __construct(
        protected MfaSession $mfaSession,
        protected EmailConfirmationService $emailConfirmationService,
        protected SocialDriverManager $socialDriverManager,
    ) {
    }

    /**
     * Log the given user into the system.
     * Will start a login of the given user but will prevent if there's
     * a reason to (MFA or Unconfirmed Email).
     * Returns a boolean to indicate the current login result.
     *
     * @throws StoppedAuthenticationException|LoginAttemptInvalidUserException
     */
    public function login(User $user, string $method, bool $remember = false): void
    {
        $sessionMethod = in_array($method, ['standard', 'ldap', 'saml2', 'oidc']) ? $method : 'standard';

        if ($user->isGuest()) {
            throw new LoginAttemptInvalidUserException('Login not allowed for guest user');
        }

        if ($this->awaitingEmailConfirmation($user) || $this->needsMfaVerification($user)) {
            $this->setLastLoginAttemptedForUser($user, $sessionMethod, $remember);

            throw new StoppedAuthenticationException($user, $this);
        }

        $this->clearLastLoginAttempted();
        $this->setSessionLoginMethod($sessionMethod);
        auth('standard')->login($user, $remember);
        if (in_array($method, ['ldap', 'saml2', 'oidc'])) {
            auth($method)->login($user, $remember);
        }
        Activity::add(ActivityType::AUTH_LOGIN, "{$method}; {$user->logDescriptor()}");
        Theme::dispatch(ThemeEvents::AUTH_LOGIN, $method, $user);

        // Authenticate on all session guards if a likely admin
        if ($user->can(Permission::UsersManage) && $user->can(Permission::UserRolesManage)) {
            $guards = ['standard', 'ldap', 'saml2', 'oidc'];
            foreach ($guards as $guard) {
                auth($guard)->login($user);
            }
        }
    }

    /**
     * Reattempt a system login after a previous stopped attempt.
     *
     * @throws Exception
     */
    public function reattemptLoginFor(User $user): void
    {
        if ($user->id !== ($this->getLastLoginAttemptUser()->id ?? null)) {
            throw new Exception('Login reattempt user does align with current session state');
        }

        $lastLoginDetails = $this->getLastLoginAttemptDetails();
        $this->login($user, $lastLoginDetails['method'], $lastLoginDetails['remember'] ?? false);
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
     * Get the details of the last login attempt.
     * Checks upon a ttl of about 1 hour since that last attempted login.
     *
     * @return array{user_id: ?string, method: ?string, remember: bool}
     */
    protected function getLastLoginAttemptDetails(): array
    {
        $value = session()->get(self::LAST_LOGIN_ATTEMPTED_SESSION_KEY);
        if (!$value) {
            return ['user_id' => null, 'method' => null, 'remember' => false];
        }

        [$id, $method, $remember, $time] = explode(':', $value);
        $hourAgo = time() - (60 * 60);
        if ($time < $hourAgo) {
            $this->clearLastLoginAttempted();

            return ['user_id' => null, 'method' => null, 'remember' => false];
        }

        return ['user_id' => $id, 'method' => $method, 'remember' => boolval($remember)];
    }

    /**
     * Set the last login-attempted user.
     * Must be only used when credentials are correct and a login could be
     * achieved, but a secondary factor has stopped the login.
     */
    protected function setLastLoginAttemptedForUser(User $user, string $method, bool $remember): void
    {
        session()->put(
            self::LAST_LOGIN_ATTEMPTED_SESSION_KEY,
            implode(':', [$user->id, $method, $remember, time()])
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
        return $this->emailConfirmationService->confirmationRequired() && !$user->email_confirmed;
    }

    /**
     * Attempt the login of a user using the given credentials.
     * Meant to mirror Laravel's default guard 'attempt' method
     * but in a manner that always routes through our login system.
     * May interrupt the flow if extra authentication requirements are imposed.
     *
     * @throws StoppedAuthenticationException
     * @throws LoginAttemptException
     */
    public function attempt(array $credentials, string $method, bool $remember = false): bool
    {
        if ($this->areCredentialsForGuest($credentials)) {
            return false;
        }

        $result = auth($method)->attempt($credentials, $remember);
        if ($result) {
            $user = auth($method)->user();
            auth($method)->logout();
            try {
                $this->login($user, $method, $remember);
            } catch (LoginAttemptInvalidUserException $e) {
                // Catch and return false for non-login accounts
                // so it looks like a normal invalid login.
                return false;
            }
        }

        return $result;
    }

    /**
     * Check if the given credentials are likely for the system guest account.
     */
    protected function areCredentialsForGuest(array $credentials): bool
    {
        if (isset($credentials['email'])) {
            return User::query()->where('email', '=', $credentials['email'])
                ->where('system_name', '=', 'public')
                ->exists();
        }

        return false;
    }

    /**
     * Logs the current user out of the application.
     * Returns an app post-redirect path.
     */
    public function logout(): string
    {
        $logoutMethod = $this->getSessionLoginMethod();
        $this->logoutFromAllGuards();
        session()->invalidate();
        session()->regenerateToken();

        return $this->shouldAutoInitiate($logoutMethod) ? '/login?prevent_auto_init=true' : '/';
    }

    /**
     * Check if login auto-initiate should be active based upon authentication config.
     */
    public function shouldAutoInitiate(?string $method = null): bool
    {
        $autoRedirect = config('auth.auto_initiate');
        if (!$autoRedirect) {
            return false;
        }

        $socialDrivers = $this->socialDriverManager->getActive();
        $authMethod = $method ?? auth_primary_method();
        $enabledMethods = auth_methods();

        return count($socialDrivers) === 0
            && count($enabledMethods) === 1
            && in_array($authMethod, ['oidc', 'saml2']);
    }

    /**
     * Get the login method stored for the current session.
     */
    public function getSessionLoginMethod(): string
    {
        return auth_session_method();
    }

    /**
     * Persist the method used for the current session login.
     */
    protected function setSessionLoginMethod(string $method): void
    {
        session()->put(self::SESSION_METHOD_KEY, $method);
    }

    /**
     * Log the user out of all supported guards to fully clear auth state.
     */
    protected function logoutFromAllGuards(): void
    {
        foreach (['standard', 'ldap', 'saml2', 'oidc'] as $guard) {
            auth($guard)->logout();
        }

        session()->remove(self::SESSION_METHOD_KEY);
        $this->clearLastLoginAttempted();
    }
}
