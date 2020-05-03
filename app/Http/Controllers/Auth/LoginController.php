<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\SocialAuthService;
use BookStack\Exceptions\LoginAttemptEmailNeededException;
use BookStack\Exceptions\LoginAttemptException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controllers\Controller;
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
     * Redirection paths
     */
    protected $redirectTo = '/';
    protected $redirectPath = '/';
    protected $redirectAfterLogout = '/login';

    protected $socialAuthService;

    /**
     * Create a new controller instance.
     */
    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->middleware('guest', ['only' => ['getLogin', 'login']]);
        $this->middleware('guard:standard,ldap', ['only' => ['login', 'logout']]);

        $this->socialAuthService = $socialAuthService;
        $this->redirectPath = url('/');
        $this->redirectAfterLogout = url('/login');
        parent::__construct();
    }

    public function username()
    {
        return config('auth.method') === 'standard' ? 'email' : 'username';
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only('username', 'email', 'password');
    }

    /**
     * Show the application login form.
     */
    public function getLogin(Request $request)
    {
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        $authMethod = config('auth.method');

        if ($request->has('email')) {
            session()->flashInput([
                'email' => $request->get('email'),
                'password' => (config('app.env') === 'demo') ? $request->get('password', '') : ''
            ]);
        }

        return view('auth.login', [
          'socialDrivers' => $socialDrivers,
          'authMethod' => $authMethod,
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            // Also log some error message
            $this->logFailedAccess($request);

            return $this->sendLockoutResponse($request);
        }

        try {
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        } catch (LoginAttemptException $exception) {
            return $this->sendLoginAttemptExceptionResponse($exception, $request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // Also log some error message
        $this->logFailedAccess($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $rules = ['password' => 'required|string'];
        $authMethod = config('auth.method');

        if ($authMethod === 'standard') {
            $rules['email'] = 'required|email';
        }

        if ($authMethod === 'ldap') {
            $rules['username'] = 'required|string';
            $rules['email'] = 'email';
        }

        $request->validate($rules);
    }

    /**
     * Send a response when a login attempt exception occurs.
     */
    protected function sendLoginAttemptExceptionResponse(LoginAttemptException $exception, Request $request)
    {
        if ($exception instanceof LoginAttemptEmailNeededException) {
            $request->flash();
            session()->flash('request-email', true);
        }

        if ($message = $exception->getMessage()) {
            $this->showWarningNotification($message);
        }

        return redirect('/login');
    }

    /**
     * Log failed accesses, for further processing by tools like Fail2Ban
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
      */
    protected function logFailedAccess($request)
    {
        $log_msg = env('FAILED_ACCESS_MESSAGE', '');

        if (!is_string($request->get($this->username())) || !is_string($log_msg) || strlen($log_msg)<1)
            return;

        $log_msg = str_replace("%u", $request->get($this->username()), $log_msg);
        error_log($log_msg, 4);
    }

}
