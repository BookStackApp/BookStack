<?php

namespace BookStack\Access;

use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Users\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Two\GoogleProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialAuthService
{
    public function __construct(
        protected Socialite $socialite,
        protected LoginService $loginService,
        protected SocialDriverManager $driverManager,
    ) {
    }

    /**
     * Start the social login path.
     *
     * @throws SocialDriverNotConfigured
     */
    public function startLogIn(string $socialDriver): RedirectResponse
    {
        $socialDriver = trim(strtolower($socialDriver));
        $this->driverManager->ensureDriverActive($socialDriver);

        return $this->getDriverForRedirect($socialDriver)->redirect();
    }

    /**
     * Start the social registration process.
     *
     * @throws SocialDriverNotConfigured
     */
    public function startRegister(string $socialDriver): RedirectResponse
    {
        $socialDriver = trim(strtolower($socialDriver));
        $this->driverManager->ensureDriverActive($socialDriver);

        return $this->getDriverForRedirect($socialDriver)->redirect();
    }

    /**
     * Handle the social registration process on callback.
     *
     * @throws UserRegistrationException
     */
    public function handleRegistrationCallback(string $socialDriver, SocialUser $socialUser): SocialUser
    {
        // Check social account has not already been used
        if (SocialAccount::query()->where('driver_id', '=', $socialUser->getId())->exists()) {
            throw new UserRegistrationException(trans('errors.social_account_in_use', ['socialAccount' => $socialDriver]), '/login');
        }

        if (User::query()->where('email', '=', $socialUser->getEmail())->exists()) {
            $email = $socialUser->getEmail();

            throw new UserRegistrationException(trans('errors.error_user_exists_different_creds', ['email' => $email]), '/login');
        }

        return $socialUser;
    }

    /**
     * Get the social user details via the social driver.
     *
     * @throws SocialDriverNotConfigured
     */
    public function getSocialUser(string $socialDriver): SocialUser
    {
        $socialDriver = trim(strtolower($socialDriver));
        $this->driverManager->ensureDriverActive($socialDriver);

        return $this->socialite->driver($socialDriver)->user();
    }

    /**
     * Handle the login process on a oAuth callback.
     *
     * @throws SocialSignInAccountNotUsed
     */
    public function handleLoginCallback(string $socialDriver, SocialUser $socialUser)
    {
        $socialDriver = trim(strtolower($socialDriver));
        $socialId = $socialUser->getId();

        // Get any attached social accounts or users
        $socialAccount = SocialAccount::query()->where('driver_id', '=', $socialId)->first();
        $isLoggedIn = auth()->check();
        $currentUser = user();
        $titleCaseDriver = Str::title($socialDriver);

        // When a user is not logged in and a matching SocialAccount exists,
        // Simply log the user into the application.
        if (!$isLoggedIn && $socialAccount !== null) {
            $this->loginService->login($socialAccount->user, $socialDriver);

            return redirect()->intended('/');
        }

        // When a user is logged in but the social account does not exist,
        // Create the social account and attach it to the user & redirect to the profile page.
        if ($isLoggedIn && $socialAccount === null) {
            $account = $this->newSocialAccount($socialDriver, $socialUser);
            $currentUser->socialAccounts()->save($account);
            session()->flash('success', trans('settings.users_social_connected', ['socialAccount' => $titleCaseDriver]));

            return redirect('/my-account/auth#social_accounts');
        }

        // When a user is logged in and the social account exists and is already linked to the current user.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id === $currentUser->id) {
            session()->flash('error', trans('errors.social_account_existing', ['socialAccount' => $titleCaseDriver]));

            return redirect('/my-account/auth#social_accounts');
        }

        // When a user is logged in, A social account exists but the users do not match.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id != $currentUser->id) {
            session()->flash('error', trans('errors.social_account_already_used_existing', ['socialAccount' => $titleCaseDriver]));

            return redirect('/my-account/auth#social_accounts');
        }

        // Otherwise let the user know this social account is not used by anyone.
        $message = trans('errors.social_account_not_used', ['socialAccount' => $titleCaseDriver]);
        if (setting('registration-enabled') && config('auth.method') !== 'ldap' && config('auth.method') !== 'saml2') {
            $message .= trans('errors.social_account_register_instructions', ['socialAccount' => $titleCaseDriver]);
        }

        throw new SocialSignInAccountNotUsed($message, '/login');
    }

    /**
     * Get the social driver manager used by this service.
     */
    public function drivers(): SocialDriverManager
    {
        return $this->driverManager;
    }

    /**
     * Fill and return a SocialAccount from the given driver name and SocialUser.
     */
    public function newSocialAccount(string $socialDriver, SocialUser $socialUser): SocialAccount
    {
        return new SocialAccount([
            'driver'    => $socialDriver,
            'driver_id' => $socialUser->getId(),
            'avatar'    => $socialUser->getAvatar(),
        ]);
    }

    /**
     * Detach a social account from a user.
     */
    public function detachSocialAccount(string $socialDriver): void
    {
        user()->socialAccounts()->where('driver', '=', $socialDriver)->delete();
    }

    /**
     * Provide redirect options per service for the Laravel Socialite driver.
     */
    protected function getDriverForRedirect(string $driverName): Provider
    {
        $driver = $this->socialite->driver($driverName);

        if ($driver instanceof GoogleProvider && config('services.google.select_account')) {
            $driver->with(['prompt' => 'select_account']);
        }

        $this->driverManager->getConfigureForRedirectCallback($driverName)($driver);

        return $driver;
    }
}
