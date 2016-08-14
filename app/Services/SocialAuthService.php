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
            throw new UserRegistrationException('This ' . $socialDriver . ' account is already in use, Try logging in via the ' . $socialDriver . ' option.', '/login');
        }

        if ($this->userRepo->getByEmail($socialUser->getEmail())) {
            $email = $socialUser->getEmail();
            throw new UserRegistrationException('The email ' . $email . ' is already in use. If you already have an account you can connect your ' . $socialDriver . ' account from your profile settings.', '/login');
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
        $currentUser = auth()->user();

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
            session()->flash('success', title_case($socialDriver) . ' account was successfully attached to your profile.');
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in and the social account exists and is already linked to the current user.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id === $currentUser->id) {
            session()->flash('error', 'This ' . title_case($socialDriver) . ' account is already attached to your profile.');
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in, A social account exists but the users do not match.
        // Change the user that the social account is assigned to.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id != $currentUser->id) {
            session()->flash('success', 'This ' . title_case($socialDriver) . ' account is already used by another user.');
            return redirect($currentUser->getEditUrl());
        }

        // Otherwise let the user know this social account is not used by anyone.
        $message = 'This ' . $socialDriver . ' account is not linked to any users. Please attach it in your profile settings';
        if (setting('registration-enabled')) {
            $message .= ' or, If you do not yet have an account, You can register an account using the ' . $socialDriver . ' option';
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

        if (!in_array($driver, $this->validSocialDrivers)) abort(404, 'Social Driver Not Found');
        if (!$this->checkDriverConfigured($driver)) throw new SocialDriverNotConfigured;

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
        auth()->user()->socialAccounts()->where('driver', '=', $socialDriver)->delete();
        \Session::flash('success', $socialDriver . ' account successfully detached');
        return redirect(auth()->user()->getEditUrl());
    }

}