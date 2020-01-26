<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\RegistrationService;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Auth\User;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    protected $registrationService;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $redirectPath = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct(SocialAuthService $socialAuthService, RegistrationService $registrationService)
    {
        $this->middleware('guest')->only(['getRegister', 'postRegister']);

        $this->socialAuthService = $socialAuthService;
        $this->registrationService = $registrationService;

        $this->redirectTo = url('/');
        $this->redirectPath = url('/');
        parent::__construct();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8',
        ]);
    }

    /**
     * Show the application registration form.
     * @throws UserRegistrationException
     */
    public function getRegister()
    {
        $this->registrationService->checkRegistrationAllowed();
        $socialDrivers = $this->socialAuthService->getActiveDrivers();
        $samlEnabled = (config('saml2.enabled') === true) && (config('saml2.auto_register') === true);
        return view('auth.register', [
            'socialDrivers' => $socialDrivers,
            'samlEnabled' => $samlEnabled,
        ]);
    }

    /**
     * Handle a registration request for the application.
     * @throws UserRegistrationException
     */
    public function postRegister(Request $request)
    {
        $this->registrationService->checkRegistrationAllowed();
        $this->validator($request->all())->validate();
        $userData = $request->all();

        try {
            $this->registrationService->registerUser($userData);
        } catch (UserRegistrationException $exception) {
            if ($exception->getMessage()) {
                $this->showErrorNotification($exception->getMessage());
            }
            return redirect($exception->redirectLocation);
        }

        $this->showSuccessNotification(trans('auth.register_success'));
        return redirect($this->redirectPath());
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
            'password' => Hash::make($data['password']),
        ]);
    }

}
