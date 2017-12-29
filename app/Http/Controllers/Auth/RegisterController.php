<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Exceptions\ConfirmationEmailException;
use BookStack\Exceptions\SocialSignInException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Repos\UserRepo;
use BookStack\Services\EmailConfirmationService;
use BookStack\Services\SocialAuthService;
use BookStack\SocialAccount;
use BookStack\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use BookStack\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    protected $socialAuthService;
    protected $emailConfirmationService;
    protected $userRepo;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $redirectPath = '/';

    /**
     * Create a new controller instance.
     *
     * @param SocialAuthService $socialAuthService
     * @param EmailConfirmationService $emailConfirmationService
     * @param UserRepo $userRepo
     */
    public function __construct(SocialAuthService $socialAuthService, EmailConfirmationService $emailConfirmationService, UserRepo $userRepo)
    {
        $this->middleware('guest')->only(['getRegister', 'postRegister', 'socialRegister']);
        $this->socialAuthService = $socialAuthService;
        $this->emailConfirmationService = $emailConfirmationService;
        $this->userRepo = $userRepo;
        $this->redirectTo = baseUrl('/');
        $this->redirectPath = baseUrl('/');
        parent::__construct();
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    /**
     * Check whether or not registrations are allowed in the app settings.
     * @throws UserRegistrationException
     */
    protected function checkRegistrationAllowed()
    {
        if (!setting('registration-enabled')) {
            throw new UserRegistrationException(trans('auth.registrations_disabled'), '/login');
        }
    }

    /**
     * Show the application registration form.
     * @return Response
     */
    public function getRegister()
    {
        $this->checkRegistrationAllowed();
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        return view('auth.register', ['socialDrivers' => $socialDrivers]);
    }

    /**
     * Handle a registration request for the application.
     * @param Request|\Illuminate\Http\Request $request
     * @return Response
     * @throws UserRegistrationException
     * @throws \Illuminate\Validation\ValidationException
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
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * The registrations flow for all users.
     * @param array $userData
     * @param bool|false|SocialAccount $socialAccount
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws UserRegistrationException
     * @throws ConfirmationEmailException
     */
    protected function registerUser(array $userData, $socialAccount = false)
    {
        if (setting('registration-restrict')) {
            $restrictedEmailDomains = explode(',', str_replace(' ', '', setting('registration-restrict')));
            $userEmailDomain = $domain = substr(strrchr($userData['email'], "@"), 1);
            if (!in_array($userEmailDomain, $restrictedEmailDomains)) {
                throw new UserRegistrationException(trans('auth.registration_email_domain_invalid'), '/register');
            }
        }

        $newUser = $this->userRepo->registerNew($userData);
        if ($socialAccount) {
            $newUser->socialAccounts()->save($socialAccount);
        }

        if (setting('registration-confirmation') || setting('registration-restrict')) {
            $newUser->save();

            try {
                $this->emailConfirmationService->sendConfirmation($newUser);
            } catch (Exception $e) {
                session()->flash('error', trans('auth.email_confirm_send_error'));
            }

            return redirect('/register/confirm');
        }

        auth()->login($newUser);
        session()->flash('success', trans('auth.register_success'));
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
        auth()->login($user);
        session()->flash('success', trans('auth.email_confirm_success'));
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

        try {
            $this->emailConfirmationService->sendConfirmation($user);
        } catch (Exception $e) {
            session()->flash('error', trans('auth.email_confirm_send_error'));
            return redirect('/register/confirm');
        }

        session()->flash('success', trans('auth.email_confirm_resent'));
        return redirect('/register/confirm');
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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws SocialSignInException
     * @throws UserRegistrationException
     * @throws \BookStack\Exceptions\SocialDriverNotConfigured
     * @throws ConfirmationEmailException
     */
    public function socialCallback($socialDriver, Request $request)
    {
        if (!session()->has('social-callback')) {
            throw new SocialSignInException(trans('errors.social_no_action_defined'), '/login');
        }

        // Check request for error information
        if ($request->has('error') && $request->has('error_description')) {
            throw new SocialSignInException(trans('errors.social_login_bad_response', [
                'socialAccount' => $socialDriver,
                'error' => $request->get('error_description'),
            ]), '/login');
        }

        $action = session()->pull('social-callback');
        if ($action == 'login') return $this->socialAuthService->handleLoginCallback($socialDriver);
        if ($action == 'register') return $this->socialRegisterCallback($socialDriver);
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

    /**
     * Register a new user after a registration callback.
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws ConfirmationEmailException
     * @throws UserRegistrationException
     * @throws \BookStack\Exceptions\SocialDriverNotConfigured
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

}