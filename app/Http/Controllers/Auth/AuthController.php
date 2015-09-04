<?php

namespace Oxbow\Http\Controllers\Auth;

use Oxbow\Exceptions\UserNotFound;
use Oxbow\Services\SocialAuthService;
use Oxbow\User;
use Validator;
use Oxbow\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

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

    protected $socialAuthService;

    /**
     * Create a new authentication controller instance.
     * @param SocialAuthService $socialAuthService
     */
    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->socialAuthService = $socialAuthService;
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

        $socialDrivers = $this->socialAuthService->getActiveDrivers();

        return view('auth.login', ['socialDrivers' => $socialDrivers]);
    }

    /**
     * Redirect to the relevant social site.
     * @param $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getSocialLogin($socialDriver)
    {
        return $this->socialAuthService->logIn($socialDriver);
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
        $user = $this->socialAuthService->getUserFromCallback($socialDriver);
        \Auth::login($user, true);
        return redirect($this->redirectPath);
    }

}
