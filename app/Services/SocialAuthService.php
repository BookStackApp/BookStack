<?php namespace Oxbow\Services;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Oxbow\Exceptions\SocialDriverNotConfigured;
use Oxbow\Exceptions\UserNotFound;
use Oxbow\Repos\UserRepo;

class SocialAuthService
{

    protected $userRepo;
    protected $socialite;

    protected $validSocialDrivers = ['google', 'github'];

    /**
     * SocialAuthService constructor.
     * @param $userRepo
     * @param $socialite
     */
    public function __construct(UserRepo $userRepo, Socialite $socialite)
    {
        $this->userRepo = $userRepo;
        $this->socialite = $socialite;
    }

    public function logIn($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);
        return $this->socialite->driver($driver)->redirect();
    }

    /**
     * Get a user from socialite after a oAuth callback.
     *
     * @param $socialDriver
     * @return mixed
     * @throws SocialDriverNotConfigured
     * @throws UserNotFound
     */
    public function getUserFromCallback($socialDriver)
    {
        $driver = $this->validateDriver($socialDriver);
        // Get user details from social driver
        $socialUser = $this->socialite->driver($driver)->user();
        $user = $this->userRepo->getByEmail($socialUser->getEmail());

        // Redirect if the email is not a current user.
        if ($user === null) {
            throw new UserNotFound('A user with the email ' . $socialUser->getEmail() . ' was not found.', '/login');
        }

        return $user;
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
        if (!$this->checklDriverConfigured($driver)) throw new SocialDriverNotConfigured;

        return $driver;
    }

    /**
     * Check a social driver has been configured correctly.
     * @param $driver
     * @return bool
     */
    private function checklDriverConfigured($driver)
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
            if ($this->checklDriverConfigured($driverName)) {
                $activeDrivers[$driverName] = true;
            }
        }
        return $activeDrivers;
    }


}