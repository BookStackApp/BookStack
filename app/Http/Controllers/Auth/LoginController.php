<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\AuthException;
use BookStack\Http\Controllers\Controller;
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
    protected $ldapService;
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     * @param \BookStack\Auth\\BookStack\Auth\Access\SocialAuthService $socialAuthService
     * @param LdapService $ldapService
     * @param \BookStack\Auth\UserRepo $userRepo
     */
    public function __construct(SocialAuthService $socialAuthService, LdapService $ldapService, UserRepo $userRepo)
    {
        $this->middleware('guest', ['only' => ['getLogin', 'postLogin']]);
        $this->socialAuthService = $socialAuthService;
        $this->ldapService = $ldapService;
        $this->userRepo = $userRepo;
        $this->redirectPath = url('/');
        $this->redirectAfterLogout = url('/login');
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
     * @throws \BookStack\Exceptions\LdapException
     */
    protected function authenticated(Request $request, Authenticatable $user)
    {
        // Explicitly log them out for now if they do no exist.
        if (!$user->exists) {
            auth()->logout($user);
        }

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

        // Sync LDAP groups if required
        if ($this->ldapService->shouldSyncGroups()) {
            $this->ldapService->syncGroups($user, $request->get($this->username()));
        }

        return redirect()->intended('/');
    }

    /**
     * Show the application login form.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getLogin(Request $request)
    {
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        $authMethod = config('auth.method');
        $samlEnabled = config('saml2_settings.enabled') == true;

        if ($request->has('email')) {
            session()->flashInput([
                'email' => $request->get('email'),
                'password' => (config('app.env') === 'demo') ? $request->get('password', '') : ''
            ]);
        }

        return view('auth.login', [
          'socialDrivers' => $socialDrivers,
          'authMethod' => $authMethod,
          'samlEnabled' => $samlEnabled,
        ]);
    }

    /**
     * Redirect to the relevant social site.
     * @param $socialDriver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \BookStack\Exceptions\SocialDriverNotConfigured
     */
    public function getSocialLogin($socialDriver)
    {
        session()->put('social-callback', 'login');
        return $this->socialAuthService->startLogIn($socialDriver);
    }
}
