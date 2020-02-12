<?php namespace BookStack\Auth\Access;

use BookStack\Auth\SocialAccount;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use BookStack\Exceptions\UserRegistrationException;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialUser;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialAuthService
{

    protected $userRepo;
    protected $socialite;
    protected $socialAccount;

    protected $validSocialDrivers = ['google', 'github', 'facebook', 'slack', 'twitter', 'azure', 'okta', 'gitlab', 'twitch', 'discord'];

    /**
     * SocialAuthService constructor.
     */
    public function __construct(UserRepo $userRepo, Socialite $socialite, SocialAccount $socialAccount)
    {
        $this->userRepo = $userRepo;
        $this->socialite = $socialite;
        $this->socialAccount = $socialAccount;
    }


    /**
     * Start the social login path.
     * @throws SocialDriverNotConfigured
     */
    public function startLogIn(string $socialDriver): RedirectResponse
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->getSocialDriver($driver)->redirect();
    }

    /**
     * Start the social registration process
     * @throws SocialDriverNotConfigured
     */
    public function startRegister(string $socialDriver): RedirectResponse
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->getSocialDriver($driver)->redirect();
    }

    /**
     * Handle the social registration process on callback.
     * @throws UserRegistrationException
     */
    public function handleRegistrationCallback(string $socialDriver, SocialUser $socialUser): SocialUser
    {
        // Check social account has not already been used
        if ($this->socialAccount->where('driver_id', '=', $socialUser->getId())->exists()) {
            throw new UserRegistrationException(trans('errors.social_account_in_use', ['socialAccount'=>$socialDriver]), '/login');
        }

        if ($this->userRepo->getByEmail($socialUser->getEmail())) {
            $email = $socialUser->getEmail();
            throw new UserRegistrationException(trans('errors.error_user_exists_different_creds', ['email' => $email]), '/login');
        }

        return $socialUser;
    }

    /**
     * Get the social user details via the social driver.
     * @throws SocialDriverNotConfigured
     */
    public function getSocialUser(string $socialDriver): SocialUser
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->socialite->driver($driver)->user();
    }

    /**
     * Handle the login process on a oAuth callback.
     * @throws SocialSignInAccountNotUsed
     */
    public function handleLoginCallback(string $socialDriver, SocialUser $socialUser)
    {
        $socialId = $socialUser->getId();

        // Get any attached social accounts or users
        $socialAccount = $this->socialAccount->where('driver_id', '=', $socialId)->first();
        $isLoggedIn = auth()->check();
        $currentUser = user();
        $titleCaseDriver = Str::title($socialDriver);

        // When a user is not logged in and a matching SocialAccount exists,
        // Simply log the user into the application.
        if (!$isLoggedIn && $socialAccount !== null) {
            auth()->login($socialAccount->user);
            return redirect()->intended('/');
        }

        // When a user is logged in but the social account does not exist,
        // Create the social account and attach it to the user & redirect to the profile page.
        if ($isLoggedIn && $socialAccount === null) {
            $this->fillSocialAccount($socialDriver, $socialUser);
            $currentUser->socialAccounts()->save($this->socialAccount);
            session()->flash('success', trans('settings.users_social_connected', ['socialAccount' => $titleCaseDriver]));
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in and the social account exists and is already linked to the current user.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id === $currentUser->id) {
            session()->flash('error', trans('errors.social_account_existing', ['socialAccount' => $titleCaseDriver]));
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in, A social account exists but the users do not match.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id != $currentUser->id) {
            session()->flash('error', trans('errors.social_account_already_used_existing', ['socialAccount' => $titleCaseDriver]));
            return redirect($currentUser->getEditUrl());
        }

        // Otherwise let the user know this social account is not used by anyone.
        $message = trans('errors.social_account_not_used', ['socialAccount' => $titleCaseDriver]);
        if (setting('registration-enabled') && config('auth.method') !== 'ldap' && config('auth.method') !== 'saml2') {
            $message .= trans('errors.social_account_register_instructions', ['socialAccount' => $titleCaseDriver]);
        }
        
        throw new SocialSignInAccountNotUsed($message, '/login');
    }

    /**
     * Ensure the social driver is correct and supported.
     * @throws SocialDriverNotConfigured
     */
    protected function validateDriver(string $socialDriver): string
    {
        $driver = trim(strtolower($socialDriver));

        if (!in_array($driver, $this->validSocialDrivers)) {
            abort(404, trans('errors.social_driver_not_found'));
        }

        if (!$this->checkDriverConfigured($driver)) {
            throw new SocialDriverNotConfigured(trans('errors.social_driver_not_configured', ['socialAccount' => Str::title($socialDriver)]));
        }

        return $driver;
    }

    /**
     * Check a social driver has been configured correctly.
     */
    protected function checkDriverConfigured(string $driver): bool
    {
        $lowerName = strtolower($driver);
        $configPrefix = 'services.' . $lowerName . '.';
        $config = [config($configPrefix . 'client_id'), config($configPrefix . 'client_secret'), config('services.callback_url')];
        return !in_array(false, $config) && !in_array(null, $config);
    }

    /**
     * Gets the names of the active social drivers.
     */
    public function getActiveDrivers(): array
    {
        $activeDrivers = [];

        foreach ($this->validSocialDrivers as $driverKey) {
            if ($this->checkDriverConfigured($driverKey)) {
                $activeDrivers[$driverKey] = $this->getDriverName($driverKey);
            }
        }

        return $activeDrivers;
    }

    /**
     * Get the presentational name for a driver.
     */
    public function getDriverName(string $driver): string
    {
        return config('services.' . strtolower($driver) . '.name');
    }

    /**
     * Check if the current config for the given driver allows auto-registration.
     */
    public function driverAutoRegisterEnabled(string $driver): bool
    {
        return config('services.' . strtolower($driver) . '.auto_register') === true;
    }

    /**
     * Check if the current config for the given driver allow email address auto-confirmation.
     */
    public function driverAutoConfirmEmailEnabled(string $driver): bool
    {
        return config('services.' . strtolower($driver) . '.auto_confirm') === true;
    }

    /**
     * Fill and return a SocialAccount from the given driver name and SocialUser.
     */
    public function fillSocialAccount(string $socialDriver, SocialUser $socialUser): SocialAccount
    {
        $this->socialAccount->fill([
            'driver'    => $socialDriver,
            'driver_id' => $socialUser->getId(),
            'avatar'    => $socialUser->getAvatar()
        ]);
        return $this->socialAccount;
    }

    /**
     * Detach a social account from a user.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function detachSocialAccount(string $socialDriver)
    {
        user()->socialAccounts()->where('driver', '=', $socialDriver)->delete();
    }

    /**
     * Provide redirect options per service for the Laravel Socialite driver
     */
    public function getSocialDriver(string $driverName): Provider
    {
        $driver = $this->socialite->driver($driverName);

        if ($driverName === 'google' && config('services.google.select_account')) {
            $driver->with(['prompt' => 'select_account']);
        }
        if ($driverName === 'azure') {
            $driver->with(['resource' => 'https://graph.windows.net']);
        }

        return $driver;
    }
}
