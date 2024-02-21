<?php

namespace BookStack\Access;

use BookStack\Activity\ActivityType;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Facades\Activity;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Users\Models\User;
use BookStack\Users\UserRepo;
use Exception;
use Illuminate\Support\Str;

class RegistrationService
{
    public function __construct(
        protected UserRepo $userRepo,
        protected EmailConfirmationService $emailConfirmationService,
    ) {
    }

    /**
     * Check if registrations are allowed in the app settings.
     *
     * @throws UserRegistrationException
     */
    public function ensureRegistrationAllowed()
    {
        if (!$this->registrationAllowed()) {
            throw new UserRegistrationException(trans('auth.registrations_disabled'), '/login');
        }
    }

    /**
     * Check if standard BookStack User registrations are currently allowed.
     * Does not prevent external-auth based registration.
     */
    protected function registrationAllowed(): bool
    {
        $authMethod = config('auth.method');
        $authMethodsWithRegistration = ['standard'];

        return in_array($authMethod, $authMethodsWithRegistration) && setting('registration-enabled');
    }

    /**
     * Attempt to find a user in the system otherwise register them as a new
     * user. For use with external auth systems since password is auto-generated.
     *
     * @throws UserRegistrationException
     */
    public function findOrRegister(string $name, string $email, string $externalId): User
    {
        $user = User::query()
            ->where('external_auth_id', '=', $externalId)
            ->first();

        if (is_null($user)) {
            $userData = [
                'name'             => $name,
                'email'            => $email,
                'password'         => Str::random(32),
                'external_auth_id' => $externalId,
            ];

            $user = $this->registerUser($userData, null, false);
        }

        return $user;
    }

    /**
     * The registrations flow for all users.
     *
     * @throws UserRegistrationException
     */
    public function registerUser(array $userData, ?SocialAccount $socialAccount = null, bool $emailConfirmed = false): User
    {
        $userEmail = $userData['email'];
        $authSystem = $socialAccount ? $socialAccount->driver : auth()->getDefaultDriver();

        // Email restriction
        $this->ensureEmailDomainAllowed($userEmail);

        // Ensure user does not already exist
        $alreadyUser = !is_null($this->userRepo->getByEmail($userEmail));
        if ($alreadyUser) {
            throw new UserRegistrationException(trans('errors.error_user_exists_different_creds', ['email' => $userEmail]), '/login');
        }

        /** @var ?bool $shouldRegister */
        $shouldRegister = Theme::dispatch(ThemeEvents::AUTH_PRE_REGISTER, $authSystem, $userData);
        if ($shouldRegister === false) {
            throw new UserRegistrationException(trans('errors.auth_pre_register_theme_prevention'), '/login');
        }

        // Create the user
        $newUser = $this->userRepo->createWithoutActivity($userData, $emailConfirmed);
        $newUser->attachDefaultRole();

        // Assign social account if given
        if ($socialAccount) {
            $newUser->socialAccounts()->save($socialAccount);
        }

        Activity::add(ActivityType::AUTH_REGISTER, $socialAccount ?? $newUser);
        Theme::dispatch(ThemeEvents::AUTH_REGISTER, $authSystem, $newUser);

        // Start email confirmation flow if required
        if ($this->emailConfirmationService->confirmationRequired() && !$emailConfirmed) {
            $newUser->save();

            try {
                $this->emailConfirmationService->sendConfirmation($newUser);
                session()->flash('sent-email-confirmation', true);
            } catch (Exception $e) {
                $message = trans('auth.email_confirm_send_error');

                throw new UserRegistrationException($message, '/register/confirm');
            }
        }

        return $newUser;
    }

    /**
     * Ensure that the given email meets any active email domain registration restrictions.
     * Throws if restrictions are active and the email does not match an allowed domain.
     *
     * @throws UserRegistrationException
     */
    protected function ensureEmailDomainAllowed(string $userEmail): void
    {
        $registrationRestrict = setting('registration-restrict');

        if (!$registrationRestrict) {
            return;
        }

        $restrictedEmailDomains = explode(',', str_replace(' ', '', $registrationRestrict));
        $userEmailDomain = mb_substr(mb_strrchr($userEmail, '@'), 1);
        if (!in_array($userEmailDomain, $restrictedEmailDomains)) {
            $redirect = $this->registrationAllowed() ? '/register' : '/login';

            throw new UserRegistrationException(trans('auth.registration_email_domain_invalid'), $redirect);
        }
    }
}
