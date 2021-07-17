<?php

namespace BookStack\Auth\Access;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\Mfa\MfaSession;
use BookStack\Auth\User;
use BookStack\Facades\Activity;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;

class LoginService
{

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
     */
    public function login(User $user, string $method): bool
    {
        if ($this->awaitingEmailConfirmation($user) || $this->needsMfaVerification($user)) {
            // TODO - Remember who last attempted a login so we can use them after such
            //  a email confirmation or mfa verification step.
            //  Create a method to fetch that attempted user for use by the email confirmation
            //  or MFA verification services.
            //  Also will need a method to 'reattemptLastAttempted' login for once
            //  the email confirmation of MFA verification steps have passed.
            //  Must ensure this remembered last attempted login is cleared upon successful login.

            // TODO - Does 'remember' still work? Probably not right now.

            // Old MFA middleware todos:

            // TODO - Need to redirect to setup if not configured AND ONLY IF NO OPTIONS CONFIGURED
            //    Might need to change up such routes to start with /configure/ for such identification.
            //    (Can't allow access to those if already configured)
            // TODO - Store mfa_pass into session for future requests?

            // TODO - Handle email confirmation handling
            //  Left BookStack\Http\Middleware\Authenticate@emailConfirmationErrorResponse in which needs
            //  be removed as an example of old behaviour.

            return false;
        }

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

        return true;
    }

    /**
     * Check if MFA verification is needed.
     */
    protected function needsMfaVerification(User $user): bool
    {
        return !$this->mfaSession->isVerifiedForUser($user) && $this->mfaSession->isRequiredForUser($user);
    }

    /**
     * Check if the given user is awaiting email confirmation.
     */
    protected function awaitingEmailConfirmation(User $user): bool
    {
        $requireConfirmation = (setting('registration-confirmation') || setting('registration-restrict'));
        return $requireConfirmation && !$user->email_confirmed;
    }

    /**
     * Attempt the login of a user using the given credentials.
     * Meant to mirror Laravel's default guard 'attempt' method
     * but in a manner that always routes through our login system.
     */
    public function attempt(array $credentials, string $method, bool $remember = false): bool
    {
        $result = auth()->attempt($credentials, $remember);
        if ($result) {
            $user = auth()->user();
            auth()->logout();
            $result = $this->login($user, $method);
        }

        return $result;
    }

}