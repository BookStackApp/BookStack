<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\EmailConfirmationService;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Auth\SocialAccount;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use BookStack\Exceptions\SocialSignInException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Laravel\Socialite\Contracts\User as SocialUser;
use Validator;

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
        $this->redirectTo = url('/');
        $this->redirectPath = url('/');
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
            'name' => 'required|min:2|max:255',
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
     * @throws UserRegistrationException
     */
    public function getRegister()
    {
        $this->checkRegistrationAllowed();
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        return view('auth.register', ['socialDrivers' => $socialDrivers]);
    }

    /**
     * Handle a registration request for the application.
     * @param Request|Request $request
     * @return RedirectResponse|Redirector
     * @throws UserRegistrationException
     */
    public function postRegister(Request $request)
    {
        $this->checkRegistrationAllowed();
        $this->validator($request->all())->validate();

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
     * @param bool $emailVerified
     * @return RedirectResponse|Redirector
     * @throws UserRegistrationException
     */
    protected function registerUser(array $userData, $socialAccount = false, $emailVerified = false)
    {
        $registrationRestrict = setting('registration-restrict');

        if ($registrationRestrict) {
            $restrictedEmailDomains = explode(',', str_replace(' ', '', $registrationRestrict));
            $userEmailDomain = $domain = mb_substr(mb_strrchr($userData['email'], "@"), 1);
            if (!in_array($userEmailDomain, $restrictedEmailDomains)) {
                throw new UserRegistrationException(trans('auth.registration_email_domain_invalid'), '/register');
            }
        }

        $newUser = $this->userRepo->registerNew($userData, $emailVerified);
        if ($socialAccount) {
            $newUser->socialAccounts()->save($socialAccount);
        }

        if ($this->emailConfirmationService->confirmationRequired() && !$emailVerified) {
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
     * Redirect to the social site for authentication intended to register.
     * @param $socialDriver
     * @return mixed
     * @throws UserRegistrationException
     * @throws SocialDriverNotConfigured
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
     * @return RedirectResponse|Redirector
     * @throws SocialSignInException
     * @throws UserRegistrationException
     * @throws SocialDriverNotConfigured
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

        // Attempt login or fall-back to register if allowed.
        $socialUser = $this->socialAuthService->getSocialUser($socialDriver);
        if ($action == 'login') {
            try {
                return $this->socialAuthService->handleLoginCallback($socialDriver, $socialUser);
            } catch (SocialSignInAccountNotUsed $exception) {
                if ($this->socialAuthService->driverAutoRegisterEnabled($socialDriver)) {
                    return $this->socialRegisterCallback($socialDriver, $socialUser);
                }
                throw $exception;
            }
        }

        if ($action == 'register') {
            return $this->socialRegisterCallback($socialDriver, $socialUser);
        }

        return redirect()->back();
    }

    /**
     * Detach a social account from a user.
     * @param $socialDriver
     * @return RedirectResponse|Redirector
     */
    public function detachSocialAccount($socialDriver)
    {
        return $this->socialAuthService->detachSocialAccount($socialDriver);
    }

    /**
     * Register a new user after a registration callback.
     * @param string $socialDriver
     * @param SocialUser $socialUser
     * @return RedirectResponse|Redirector
     * @throws UserRegistrationException
     */
    protected function socialRegisterCallback(string $socialDriver, SocialUser $socialUser)
    {
        $socialUser = $this->socialAuthService->handleRegistrationCallback($socialDriver, $socialUser);
        $socialAccount = $this->socialAuthService->fillSocialAccount($socialDriver, $socialUser);
        $emailVerified = $this->socialAuthService->driverAutoConfirmEmailEnabled($socialDriver);

        // Create an array of the user data to create a new user instance
        $userData = [
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'password' => str_random(30)
        ];
        return $this->registerUser($userData, $socialAccount, $emailVerified);
    }
}
