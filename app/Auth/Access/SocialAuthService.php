<?php namespace BookStack\Auth\Access;

use BookStack\Auth\SocialAccount;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use BookStack\Exceptions\UserRegistrationException;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Contracts\User as SocialUser;

class SocialAuthService
{

    protected $userRepo;
    protected $socialite;
    protected $socialAccount;

    protected $validSocialDrivers = ['google', 'github', 'facebook', 'slack', 'twitter', 'azure', 'okta', 'gitlab', 'twitch', 'discord'];

    /**
     * SocialAuthService constructor.
     * @param \BookStack\Auth\UserRepo      $userRepo
     * @param Socialite     $socialite
     * @param SocialAccount $socialAccount
     */
    public function __construct(UserRepo $userRepo, Socialite $socialite, SocialAccount $socialAccount)
    {
        $this->userRepo = $userRepo;
        $this->socialite = $socialite;
        $this->socialAccount = $socialAccount;
    }


    /**
     * Start the social login path.
     * @param string $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws SocialDriverNotConfigured
     */
    public function startLogIn($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->redirectToSocialProvider($driver);
    }

    /**
     * Start the social registration process
     * @param string $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws SocialDriverNotConfigured
     */
    public function startRegister($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->redirectToSocialProvider($driver);
    }

    /**
     * Handle the social registration process on callback.
     * @param string $socialDriver
     * @param SocialUser $socialUser
     * @return SocialUser
     * @throws UserRegistrationException
     */
    public function handleRegistrationCallback(string $socialDriver, SocialUser $socialUser)
    {
        // Check social account has not already been used
        if ($this->socialAccount->where('driver_id', '=', $socialUser->getId())->exists()) {
            throw new UserRegistrationException(trans('errors.social_account_in_use', ['socialAccount'=>$socialDriver]), '/login');
        }

        if ($this->userRepo->getByEmail($socialUser->getEmail())) {
            $email = $socialUser->getEmail();
            throw new UserRegistrationException(trans('errors.social_account_in_use', ['socialAccount'=>$socialDriver, 'email' => $email]), '/login');
        }

        return $socialUser;
    }

    /**
     * Get the social user details via the social driver.
     * @param string $socialDriver
     * @return SocialUser
     * @throws SocialDriverNotConfigured
     */
    public function getSocialUser(string $socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->socialite->driver($driver)->user();
    }

    /**
     * Handle the login process on a oAuth callback.
     * @param $socialDriver
     * @param SocialUser $socialUser
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws SocialSignInAccountNotUsed
     */
    public function handleLoginCallback($socialDriver, SocialUser $socialUser)
    {
        $socialId = $socialUser->getId();

        // Get any attached social accounts or users
        $socialAccount = $this->socialAccount->where('driver_id', '=', $socialId)->first();
        $isLoggedIn = auth()->check();
        $currentUser = user();

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
            session()->flash('success', trans('settings.users_social_connected', ['socialAccount' => title_case($socialDriver)]));
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in and the social account exists and is already linked to the current user.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id === $currentUser->id) {
            session()->flash('error', trans('errors.social_account_existing', ['socialAccount' => title_case($socialDriver)]));
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in, A social account exists but the users do not match.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id != $currentUser->id) {
            session()->flash('error', trans('errors.social_account_already_used_existing', ['socialAccount' => title_case($socialDriver)]));
            return redirect($currentUser->getEditUrl());
        }

        // Otherwise let the user know this social account is not used by anyone.
        $message = trans('errors.social_account_not_used', ['socialAccount' => title_case($socialDriver)]);
        if (setting('registration-enabled')) {
            $message .= trans('errors.social_account_register_instructions', ['socialAccount' => title_case($socialDriver)]);
        }
        
        throw new SocialSignInAccountNotUsed($message, '/login');
    }

    /**
     * Ensure the social driver is correct and supported.
     *
     * @param $socialDriver
     * @return string
     * @throws SocialDriverNotConfigured
     */
    private function validateDriver($socialDriver)
    {
        $driver = trim(strtolower($socialDriver));

        if (!in_array($driver, $this->validSocialDrivers)) {
            abort(404, trans('errors.social_driver_not_found'));
        }
        if (!$this->checkDriverConfigured($driver)) {
            throw new SocialDriverNotConfigured(trans('errors.social_driver_not_configured', ['socialAccount' => title_case($socialDriver)]));
        }

        return $driver;
    }

    /**
     * Check a social driver has been configured correctly.
     * @param $driver
     * @return bool
     */
    private function checkDriverConfigured($driver)
    {
        $lowerName = strtolower($driver);
        $configPrefix = 'services.' . $lowerName . '.';
        $config = [config($configPrefix . 'client_id'), config($configPrefix . 'client_secret'), config('services.callback_url')];
        return !in_array(false, $config) && !in_array(null, $config);
    }

    /**
     * Gets the names of the active social drivers.
     * @return array
     */
    public function getActiveDrivers()
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
     * @param $driver
     * @return mixed
     */
    public function getDriverName($driver)
    {
        return config('services.' . strtolower($driver) . '.name');
    }

    /**
     * Check if the current config for the given driver allows auto-registration.
     * @param string $driver
     * @return bool
     */
    public function driverAutoRegisterEnabled(string $driver)
    {
        return config('services.' . strtolower($driver) . '.auto_register') === true;
    }

    /**
     * Check if the current config for the given driver allow email address auto-confirmation.
     * @param string $driver
     * @return bool
     */
    public function driverAutoConfirmEmailEnabled(string $driver)
    {
        return config('services.' . strtolower($driver) . '.auto_confirm') === true;
    }

    /**
     * @param string $socialDriver
     * @param SocialUser $socialUser
     * @return SocialAccount
     */
    public function fillSocialAccount($socialDriver, $socialUser)
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
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function detachSocialAccount($socialDriver)
    {
        user()->socialAccounts()->where('driver', '=', $socialDriver)->delete();
        session()->flash('success', trans('settings.users_social_disconnected', ['socialAccount' => title_case($socialDriver)]));
        return redirect(user()->getEditUrl());
    }

    /**
     * Provide redirect options per service for the Laravel Socialite driver
     * @param $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToSocialProvider($driver)
    {
        if ($driver == 'google' && config('services.google.select_account'))
        {
            return $this->socialite->driver($driver)->with(['prompt' => 'select_account'])->redirect();
        }

        return $this->socialite->driver($driver)->redirect();
    }
}
