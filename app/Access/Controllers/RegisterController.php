<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Access\RegistrationService;
use BookStack\Access\SocialAuthService;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controller;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    protected SocialAuthService $socialAuthService;
    protected RegistrationService $registrationService;
    protected LoginService $loginService;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        SocialAuthService $socialAuthService,
        RegistrationService $registrationService,
        LoginService $loginService
    ) {
        $this->middleware('guest');
        $this->middleware('guard:standard');

        $this->socialAuthService = $socialAuthService;
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;
    }

    /**
     * Show the application registration form.
     *
     * @throws UserRegistrationException
     */
    public function getRegister()
    {
        $this->registrationService->ensureRegistrationAllowed();
        $socialDrivers = $this->socialAuthService->getActiveDrivers();

        return view('auth.register', [
            'socialDrivers' => $socialDrivers,
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @throws UserRegistrationException
     * @throws StoppedAuthenticationException
     */
    public function postRegister(Request $request)
    {
        $this->registrationService->ensureRegistrationAllowed();
        $this->validator($request->all())->validate();
        $userData = $request->all();

        try {
            $user = $this->registrationService->registerUser($userData);
            $this->loginService->login($user, auth()->getDefaultDriver());
        } catch (UserRegistrationException $exception) {
            if ($exception->getMessage()) {
                $this->showErrorNotification($exception->getMessage());
            }

            return redirect($exception->redirectLocation);
        }

        $this->showSuccessNotification(trans('auth.register_success'));

        return redirect('/');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data): ValidatorContract
    {
        return Validator::make($data, [
            'name'     => ['required', 'min:2', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::default()],
        ]);
    }
}
