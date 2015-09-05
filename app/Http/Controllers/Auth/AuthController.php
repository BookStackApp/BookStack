<?php

namespace Oxbow\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Oxbow\Exceptions\SocialSignInException;
use Oxbow\Exceptions\UserRegistrationException;
use Oxbow\Repos\UserRepo;
use Oxbow\Services\EmailConfirmationService;
use Oxbow\Services\Facades\Setting;
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
    protected $emailConfirmationService;
    protected $userRepo;

    /**
     * Create a new authentication controller instance.
     * @param SocialAuthService        $socialAuthService
     * @param EmailConfirmationService $emailConfirmationService
     * @param UserRepo                 $userRepo
     */
    public function __construct(SocialAuthService $socialAuthService, EmailConfirmationService $emailConfirmationService, UserRepo $userRepo)
    {
        $this->middleware('guest', ['only' => ['getLogin', 'postLogin', 'getRegister']]);
        $this->socialAuthService = $socialAuthService;
        $this->emailConfirmationService = $emailConfirmationService;
        $this->userRepo = $userRepo;
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
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
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

    protected function checkRegistrationAllowed()
    {
        if(!\Setting::get('registration-enabled')) {
            throw new UserRegistrationException('Registrations are currently disabled.', '/login');
        }
    }

    /**
     * Show the application registration form.
     *
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
     *
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

        if(\Setting::get('registration-restrict')) {
            $restrictedEmailDomains = explode(',', str_replace(' ', '', \Setting::get('registration-restrict')));
            $userEmailDomain = $domain = substr(strrchr($request->get('email'), "@"), 1);
            if(!in_array($userEmailDomain, $restrictedEmailDomains)) {
                throw new UserRegistrationException('That email domain does not have access to this application', '/register');
            }
        }

        $newUser = $this->create($request->all());
        $newUser->attachRoleId(\Setting::get('registration-role'), 1);

        if(\Setting::get('registration-confirmation') || \Setting::get('registration-restrict')) {
            $newUser->email_confirmed = false;
            $newUser->save();
            $this->emailConfirmationService->sendConfirmation($newUser);
            return redirect('/register/confirm');
        }

        auth()->login($newUser);
        return redirect($this->redirectPath());
    }

    /**
     * Show the page to tell the user to check thier email
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
        \Session::flash('success', 'Confirmation email resent, Please check your inbox.');
        return redirect('/register/confirm');
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
        return $this->socialAuthService->startLogIn($socialDriver);
    }

    /**
     * The callback for social login services.
     *
     * @param $socialDriver
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws SocialSignInException
     */
    public function socialCallback($socialDriver)
    {
        return $this->socialAuthService->handleCallback($socialDriver);
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
