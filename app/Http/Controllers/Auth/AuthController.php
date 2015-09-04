<?php

namespace Oxbow\Http\Controllers\Auth;

use Oxbow\Exceptions\SocialDriverNotConfigured;
use Oxbow\Exceptions\UserNotFound;
use Oxbow\Repos\UserRepo;
use Oxbow\User;
use Validator;
use Oxbow\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Laravel\Socialite\Contracts\Factory as Socialite;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $loginPath = '/login';
    protected $redirectPath = '/';
    protected $redirectAfterLogout = '/login';

    protected $validSocialDrivers = ['google', 'github'];

    protected $socialite;
    protected $userRepo;

    /**
     * Create a new authentication controller instance.
     * @param Socialite $socialite
     * @param UserRepo  $userRepo
     */
    public function __construct(Socialite $socialite, UserRepo $userRepo)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->socialite = $socialite;
        $this->userRepo = $userRepo;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {

        if (view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }

        $socialDrivers = $this->getActiveSocialDrivers();

        return view('auth.login', ['socialDrivers' => $socialDrivers]);
    }

    /**
     * Redirect to the relevant social site.
     * @param $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getSocialLogin($socialDriver)
    {
        $driver = $this->validateSocialDriver($socialDriver);
        return $this->socialite->driver($driver)->redirect();
    }

    /**
     * The callback for social login services.
     *
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws UserNotFound
     */
    public function socialCallback($socialDriver)
    {
        $driver = $this->validateSocialDriver($socialDriver);
        // Get user details from social driver
        $socialUser = $this->socialite->driver($driver)->user();
        $user = $this->userRepo->getByEmail($socialUser->getEmail());

        // Redirect if the email is not a current user.
        if ($user === null) {
            throw new UserNotFound('A user with the email ' . $socialUser->getEmail() . ' was not found.', '/login');
        }

        \Auth::login($user, true);
        return redirect($this->redirectPath);
    }

    /**
     * Ensure the social driver is correct and supported.
     *
     * @param $socialDriver
     * @return string
     * @throws SocialDriverNotConfigured
     */
    protected function validateSocialDriver($socialDriver)
    {
        $driver = trim(strtolower($socialDriver));

        if (!in_array($driver, $this->validSocialDrivers)) abort(404, 'Social Driver Not Found');
        if(!$this->checkSocialDriverConfigured($driver)) throw new SocialDriverNotConfigured;

        return $driver;
    }

    /**
     * Check a social driver has been configured correctly.
     * @param $driver
     * @return bool
     */
    protected function checkSocialDriverConfigured($driver)
    {
        $upperName = strtoupper($driver);
        $config = [env($upperName . '_APP_ID', false), env($upperName . '_APP_SECRET', false), env('APP_URL', false)];
        return (!in_array(false, $config) && !in_array(null, $config));
    }

    /**
     * Gets the names of the active social drivers.
     * @return array
     */
    protected function getActiveSocialDrivers()
    {
        $activeDrivers = [];
        foreach($this->validSocialDrivers as $driverName) {
            if($this->checkSocialDriverConfigured($driverName)) {
                $activeDrivers[$driverName] = true;
            }
        }
        return $activeDrivers;
    }
}
