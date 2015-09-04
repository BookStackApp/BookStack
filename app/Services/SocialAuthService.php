<?php namespace Oxbow\Services;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Oxbow\Exceptions\SocialDriverNotConfigured;
use Oxbow\Exceptions\SocialSignInException;
use Oxbow\Repos\UserRepo;
use Oxbow\SocialAccount;
use Oxbow\User;

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
     * @param $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws SocialDriverNotConfigured
     */
    public function startLogIn($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->socialite->driver($driver)->redirect();
    }

    /**
     * Get a user from socialite after a oAuth callback.
     *
     * @param $socialDriver
     * @return User
     * @throws SocialDriverNotConfigured
     * @throws SocialSignInException
     */
    public function handleCallback($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);

        // Get user details from social driver
        $socialUser = $this->socialite->driver($driver)->user();
        $socialId = $socialUser->getId();

        // Get any attached social accounts or users
        $socialAccount = $this->socialAccount->where('driver_id', '=', $socialId)->first();
        $user = $this->userRepo->getByEmail($socialUser->getEmail());
        $isLoggedIn = \Auth::check();
        $currentUser = \Auth::user();

        // When a user is not logged in but a matching SocialAccount exists,
        // Log the user found on the SocialAccount into the application.
        if (!$isLoggedIn && $socialAccount !== null) {
            return $this->logUserIn($socialAccount->user);
        }

        // When a user is logged in but the social account does not exist,
        // Create the social account and attach it to the user & redirect to the profile page.
        if ($isLoggedIn && $socialAccount === null) {
            $this->fillSocialAccount($socialDriver, $socialUser);
            $currentUser->socialAccounts()->save($this->socialAccount);
            \Session::flash('success', title_case($socialDriver) . ' account was successfully attached to your profile.');
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in and the social account exists and is already linked to the current user.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id === $currentUser->id) {
            \Session::flash('error', 'This ' . title_case($socialDriver) . ' account is already attached to your profile.');
            return redirect($currentUser->getEditUrl());
        }

        // When a user is logged in, A social account exists but the users do not match.
        // Change the user that the social account is assigned to.
        if ($isLoggedIn && $socialAccount !== null && $socialAccount->user->id != $currentUser->id) {
            $socialAccount->user_id = $currentUser->id;
            $socialAccount->save();
            \Session::flash('success', 'This ' . title_case($socialDriver) . ' account is now attached to your profile.');
        }

        if ($user === null) {
            throw new SocialSignInException('A system user with the email ' . $socialUser->getEmail() .
                ' was not found and this ' . $socialDriver . ' account is not linked to any users.', '/login');
        }
        return $this->authenticateUserWithNewSocialAccount($user, $socialUser, $socialUser);
    }

    /**
     * Logs a user in and creates a new social account entry for future usage.
     * @param User                              $user
     * @param string                            $socialDriver
     * @param \Laravel\Socialite\Contracts\User $socialUser
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function authenticateUserWithNewSocialAccount($user, $socialDriver, $socialUser)
    {
        $this->fillSocialAccount($socialDriver, $socialUser);
        $user->socialAccounts()->save($this->socialAccount);
        return $this->logUserIn($user);
    }

    private function logUserIn($user)
    {
        \Auth::login($user);
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
        $upperName = strtoupper($driver);
        $config = [env($upperName . '_APP_ID', false), env($upperName . '_APP_SECRET', false), env('APP_URL', false)];
        return (!in_array(false, $config) && !in_array(null, $config));
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
     * @param $socialDriver
     * @param $socialUser
     */
    private function fillSocialAccount($socialDriver, $socialUser)
    {
        $this->socialAccount->fill([
            'driver'    => $socialDriver,
            'driver_id' => $socialUser->getId(),
            'avatar'    => $socialUser->getAvatar()
        ]);
    }

    /**
     * Detach a social account from a user.
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function detachSocialAccount($socialDriver)
    {
        \Auth::user()->socialAccounts()->where('driver', '=', $socialDriver)->delete();
        \Session::flash('success', $socialDriver . ' account successfully detached');
        return redirect(\Auth::user()->getEditUrl());
    }

}