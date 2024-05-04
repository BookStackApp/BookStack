<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Access\RegistrationService;
use BookStack\Access\SocialDriverManager;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controller;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct(
        protected SocialDriverManager $socialDriverManager,
        protected RegistrationService $registrationService,
        protected LoginService $loginService
    ) {
        $this->middleware('guest');
        $this->middleware('guard:standard');
    }

    /**
     * Show the application registration form.
     *
     * @throws UserRegistrationException
     */
    public function getRegister()
    {
        $this->registrationService->ensureRegistrationAllowed();
        $socialDrivers = $this->socialDriverManager->getActive();

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
            // Basic honey for bots that must not be filled in
            'username' => ['prohibited'],
        ]);
    }
}
