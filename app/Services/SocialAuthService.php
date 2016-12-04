<?php namespace BookStack\Services;

use Laravel\Socialite\Contracts\Factory as Socialite;
use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Repos\UserRepo;
use BookStack\SocialAccount;

class SocialAuthService
{

    protected $userRepo;
    protected $socialite;
    protected $socialAccount;

    protected $validSocialDrivers = ['google', 'github'];

    /**
     * SocialAuthService constructor.
     * @param UserRepo      $userRepo
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
        return $this->socialite->driver($driver)->redirect();
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
        return $this->socialite->driver($driver)->redirect();
    }

    /**
     * Handle the social registration process on callback.
     * @param $socialDriver
     * @return \Laravel\Socialite\Contracts\User
     * @throws SocialDriverNotConfigured
     * @throws UserRegistrationException
     */
    public function handleRegistrationCallback($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);

        // Get user details from social driver
        $socialUser = $this->socialite->driver($driver)->user();

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
     * Handle the login process on a oAuth callback.
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws SocialDriverNotConfigured
     * @throws SocialSignInException
     */
    public function handleLoginCallback($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);

        // Get user details from social driver
        $socialUser = $this->socialite->driver($driver)->user();
        $socialId = $socialUser->getId();

        // Get any attached social accounts or users
        $socialAccount = $this->socialAccount->where('driver_id', '=', $socialId)->first();
        $user = $this->userRepo->getByEmail($socialUser->getEmail());
        $isLoggedIn = auth()->check();
        $currentUser = user();

        // When a user is not logged in and a matching SocialAccount exists,
        // Simply log the user into the application.
        if (!$isLoggedIn && $socialAccount !== null) {
            return $this->logUserIn($socialAccount->user);
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
        
        throw new SocialSignInException($message . '.', '/login');
    }


    private function logUserIn($user)
    {
        auth()->login($user);
        return redirect('/');
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

        if (!in_array($driver, $this->validSocialDrivers)) abort(404, trans('errors.social_driver_not_found'));
        if (!$this->checkDriverConfigured($driver)) throw new SocialDriverNotConfigured(trans('errors.social_driver_not_configured', ['socialAccount' => title_case($socialDriver)]));

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
        foreach ($this->validSocialDrivers as $driverName) {
            if ($this->checkDriverConfigured($driverName)) {
                $activeDrivers[$driverName] = true;
            }
        }
        return $activeDrivers;
    }

    /**
     * @param string                            $socialDriver
     * @param \Laravel\Socialite\Contracts\User $socialUser
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
        session();
        user()->socialAccounts()->where('driver', '=', $socialDriver)->delete();
        session()->flash('success', trans('settings.users_social_disconnected', ['socialAccount' => title_case($socialDriver)]));
        return redirect(user()->getEditUrl());
    }

}