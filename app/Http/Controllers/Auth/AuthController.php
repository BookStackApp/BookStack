<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Exceptions\AuthException;
use BookStack\Exceptions\PrettyException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use BookStack\Exceptions\SocialSignInException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Repos\UserRepo;
use BookStack\Services\EmailConfirmationService;
use BookStack\Services\SocialAuthService;
use BookStack\SocialAccount;
use Validator;
use BookStack\Http\Controllers\Controller;
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

    protected $redirectPath = '/';
    protected $redirectAfterLogout = '/login';
    protected $username = 'email';


    protected $socialAuthService;
    protected $emailConfirmationService;
    protected $userRepo;

    /**
     * Create a new authentication controller instance.
     * @param SocialAuthService $socialAuthService
     * @param EmailConfirmationService $emailConfirmationService
     * @param UserRepo $userRepo
     */
    public function __construct(SocialAuthService $socialAuthService, EmailConfirmationService $emailConfirmationService, UserRepo $userRepo)
    {
        $this->middleware('guest', ['only' => ['getLogin', 'postLogin', 'getRegister', 'postRegister']]);
        $this->socialAuthService = $socialAuthService;
        $this->emailConfirmationService = $emailConfirmationService;
        $this->userRepo = $userRepo;
        $this->username = config('auth.method') === 'standard' ? 'email' : 'username';
        parent::__construct();
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    protected function checkRegistrationAllowed()
    {
        if (!setting('registration-enabled')) {
            throw new UserRegistrationException('Registrations are currently disabled.', '/login');
        }
    }

    /**
     * Show the application registration form.
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        $this->checkRegistrationAllowed();
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        return view('auth.register', ['socialDrivers' => $socialDrivers]);
    }

    /**
     * Handle a registration request for the application.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws UserRegistrationException
     */
    public function postRegister(Request $request)
    {
        $this->checkRegistrationAllowed();
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $userData = $request->all();
        return $this->registerUser($userData);
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

        if (!$user->exists && $user->email === null && !$request->has('email')) {
            $request->flash();
            session()->flash('request-email', true);
            return redirect('/login');
        }

        if (!$user->exists && $user->email === null && $request->has('email')) {
            $user->email = $request->get('email');
        }

        if (!$user->exists) {

            // Check for users with same email already
            $alreadyUser = $user->newQuery()->where('email', '=', $user->email)->count() > 0;
            if ($alreadyUser) {
                throw new AuthException('A user with the email ' . $user->email . ' already exists but with different credentials.');
            }

            $user->save();
            $this->userRepo->attachDefaultRole($user);
            auth()->login($user);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Register a new user after a registration callback.
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws UserRegistrationException
     */
    protected function socialRegisterCallback($socialDriver)
    {
        $socialUser = $this->socialAuthService->handleRegistrationCallback($socialDriver);
        $socialAccount = $this->socialAuthService->fillSocialAccount($socialDriver, $socialUser);

        // Create an array of the user data to create a new user instance
        $userData = [
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'password' => str_random(30)
        ];
        return $this->registerUser($userData, $socialAccount);
    }

    /**
     * The registrations flow for all users.
     * @param array $userData
     * @param bool|false|SocialAccount $socialAccount
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws UserRegistrationException
     * @throws \BookStack\Exceptions\ConfirmationEmailException
     */
    protected function registerUser(array $userData, $socialAccount = false)
    {
        if (setting('registration-restrict')) {
            $restrictedEmailDomains = explode(',', str_replace(' ', '', setting('registration-restrict')));
            $userEmailDomain = $domain = substr(strrchr($userData['email'], "@"), 1);
            if (!in_array($userEmailDomain, $restrictedEmailDomains)) {
                throw new UserRegistrationException('That email domain does not have access to this application', '/register');
            }
        }

        $newUser = $this->userRepo->registerNew($userData);
        if ($socialAccount) {
            $newUser->socialAccounts()->save($socialAccount);
        }

        if (setting('registration-confirmation') || setting('registration-restrict')) {
            $newUser->save();
            $this->emailConfirmationService->sendConfirmation($newUser);
            return redirect('/register/confirm');
        }

        auth()->login($newUser);
        session()->flash('success', 'Thanks for signing up! You are now registered and signed in.');
        return redirect($this->redirectPath());
    }

    /**
     * Show the page to tell the user to check their email
     * and confirm their address.
     */
    public function getRegisterConfirmation()
    {
        return view('auth/register-confirm');
    }

    /**
     * View the confirmation email as a standard web page.
     * @param $token
     * @return \Illuminate\View\View
     * @throws UserRegistrationException
     */
    public function viewConfirmEmail($token)
    {
        $confirmation = $this->emailConfirmationService->getEmailConfirmationFromToken($token);
        return view('emails/email-confirmation', ['token' => $confirmation->token]);
    }

    /**
     * Confirms an email via a token and logs the user into the system.
     * @param $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws UserRegistrationException
     */
    public function confirmEmail($token)
    {
        $confirmation = $this->emailConfirmationService->getEmailConfirmationFromToken($token);
        $user = $confirmation->user;
        $user->email_confirmed = true;
        $user->save();
        auth()->login($confirmation->user);
        session()->flash('success', 'Your email has been confirmed!');
        $this->emailConfirmationService->deleteConfirmationsByUser($user);
        return redirect($this->redirectPath);
    }

    /**
     * Shows a notice that a user's email address has not been confirmed,
     * Also has the option to re-send the confirmation email.
     * @return \Illuminate\View\View
     */
    public function showAwaitingConfirmation()
    {
        return view('auth/user-unconfirmed');
    }

    /**
     * Resend the confirmation email
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function resendConfirmation(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email'
        ]);
        $user = $this->userRepo->getByEmail($request->get('email'));
        $this->emailConfirmationService->sendConfirmation($user);
        session()->flash('success', 'Confirmation email resent, Please check your inbox.');
        return redirect('/register/confirm');
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

    /**
     * Redirect to the social site for authentication intended to register.
     * @param $socialDriver
     * @return mixed
     */
    public function socialRegister($socialDriver)
    {
        $this->checkRegistrationAllowed();
        session()->put('social-callback', 'register');
        return $this->socialAuthService->startRegister($socialDriver);
    }

    /**
     * The callback for social login services.
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws SocialSignInException
     */
    public function socialCallback($socialDriver)
    {
        if (session()->has('social-callback')) {
            $action = session()->pull('social-callback');
            if ($action == 'login') {
                return $this->socialAuthService->handleLoginCallback($socialDriver);
            } elseif ($action == 'register') {
                return $this->socialRegisterCallback($socialDriver);
            }
        } else {
            throw new SocialSignInException('No action defined', '/login');
        }
        return redirect()->back();
    }

    /**
     * Detach a social account from a user.
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function detachSocialAccount($socialDriver)
    {
        return $this->socialAuthService->detachSocialAccount($socialDriver);
    }

}
