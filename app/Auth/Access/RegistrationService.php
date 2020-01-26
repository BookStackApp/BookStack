<?php namespace BookStack\Auth\Access;

use BookStack\Auth\SocialAccount;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\UserRegistrationException;
use Exception;

class RegistrationService
{

    protected $userRepo;
    protected $emailConfirmationService;

    /**
     * RegistrationService constructor.
     */
    public function __construct(UserRepo $userRepo, EmailConfirmationService $emailConfirmationService)
    {
        $this->userRepo = $userRepo;
        $this->emailConfirmationService = $emailConfirmationService;
    }


    /**
     * Check whether or not registrations are allowed in the app settings.
     * @throws UserRegistrationException
     */
    public function checkRegistrationAllowed()
    {
        if (!setting('registration-enabled') || config('auth.method') === 'ldap') {
            throw new UserRegistrationException(trans('auth.registrations_disabled'), '/login');
        }
    }

    /**
     * The registrations flow for all users.
     * @throws UserRegistrationException
     */
    public function registerUser(array $userData, ?SocialAccount $socialAccount = null, bool $emailVerified = false)
    {
        $registrationRestrict = setting('registration-restrict');

        if ($registrationRestrict) {
            $restrictedEmailDomains = explode(',', str_replace(' ', '', $registrationRestrict));
            $userEmailDomain = $domain = mb_substr(mb_strrchr($userData['email'], "@"), 1);
            if (!in_array($userEmailDomain, $restrictedEmailDomains)) {
                throw new UserRegistrationException(trans('auth.registration_email_domain_invalid'), '/register');
            }
        }

        $newUser = $this->userRepo->registerNew($userData, $emailVerified);

        if ($socialAccount) {
            $newUser->socialAccounts()->save($socialAccount);
        }

        if ($this->emailConfirmationService->confirmationRequired() && !$emailVerified) {
            $newUser->save();
            $message = '';

            try {
                $this->emailConfirmationService->sendConfirmation($newUser);
            } catch (Exception $e) {
                $message = trans('auth.email_confirm_send_error');
            }

            throw new UserRegistrationException($message, '/register/confirm');
        }

        auth()->login($newUser);
    }

}