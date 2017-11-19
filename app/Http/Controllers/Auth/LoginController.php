<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Exceptions\AuthException;
use BookStack\Http\Controllers\Controller;
use BookStack\Repos\UserRepo;
use BookStack\Services\SocialAuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $redirectPath = '/';
    protected $redirectAfterLogout = '/login';

    protected $socialAuthService;
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     * @param SocialAuthService $socialAuthService
     * @param UserRepo $userRepo
     */
    public function __construct(SocialAuthService $socialAuthService, UserRepo $userRepo)
    {
        $this->middleware('guest', ['only' => ['getLogin', 'postLogin']]);
        $this->socialAuthService = $socialAuthService;
        $this->userRepo = $userRepo;
        $this->redirectPath = baseUrl('/');
        $this->redirectAfterLogout = baseUrl('/login');
        parent::__construct();
    }

    public function username()
    {
        return config('auth.method') === 'standard' ? 'email' : 'username';
    }

    /**
     * Overrides the action when a user is authenticated.
     * If the user authenticated but does not exist in the user table we create them.
     * @param Request $request
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws AuthException
     */
    protected function authenticated(Request $request, Authenticatable $user)
    {
        // Explicitly log them out for now if they do no exist.
        if (!$user->exists) auth()->logout($user);

        if (!$user->exists && $user->email === null && !$request->filled('email')) {
            $request->flash();
            session()->flash('request-email', true);
            return redirect('/login');
        }

        if (!$user->exists && $user->email === null && $request->filled('email')) {
            $user->email = $request->get('email');
        }

        if (!$user->exists) {

            // Check for users with same email already
            $alreadyUser = $user->newQuery()->where('email', '=', $user->email)->count() > 0;
            if ($alreadyUser) {
                throw new AuthException(trans('errors.error_user_exists_different_creds', ['email' => $user->email]));
            }

            $user->save();
            $this->userRepo->attachDefaultRole($user);
            auth()->login($user);
        }

        $path = session()->pull('url.intended', '/');
        $path = baseUrl($path, true);
        return redirect($path);
    }

    /**
     * Show the application login form.
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        $authMethod = config('auth.method');
        return view('auth/login', ['socialDrivers' => $socialDrivers, 'authMethod' => $authMethod]);
    }

    /**
     * Redirect to the relevant social site.
     * @param $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getSocialLogin($socialDriver)
    {
        session()->put('social-callback', 'login');
        return $this->socialAuthService->startLogIn($socialDriver);
    }
}