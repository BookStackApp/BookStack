<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Access\RegistrationService;
use BookStack\Access\SocialAuthService;
use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use BookStack\Exceptions\SocialSignInException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;

class SocialController extends Controller
{
    protected SocialAuthService $socialAuthService;
    protected RegistrationService $registrationService;
    protected LoginService $loginService;

    /**
     * SocialController constructor.
     */
    public function __construct(
        SocialAuthService $socialAuthService,
        RegistrationService $registrationService,
        LoginService $loginService
    ) {
        $this->middleware('guest')->only(['register']);
        $this->socialAuthService = $socialAuthService;
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;
    }

    /**
     * Redirect to the relevant social site.
     *
     * @throws SocialDriverNotConfigured
     */
    public function login(string $socialDriver)
    {
        session()->put('social-callback', 'login');

        return $this->socialAuthService->startLogIn($socialDriver);
    }

    /**
     * Redirect to the social site for authentication intended to register.
     *
     * @throws SocialDriverNotConfigured
     * @throws UserRegistrationException
     */
    public function register(string $socialDriver)
    {
        $this->registrationService->ensureRegistrationAllowed();
        session()->put('social-callback', 'register');

        return $this->socialAuthService->startRegister($socialDriver);
    }

    /**
     * The callback for social login services.
     *
     * @throws SocialSignInException
     * @throws SocialDriverNotConfigured
     * @throws UserRegistrationException
     */
    public function callback(Request $request, string $socialDriver)
    {
        if (!session()->has('social-callback')) {
            throw new SocialSignInException(trans('errors.social_no_action_defined'), '/login');
        }

        // Check request for error information
        if ($request->has('error') && $request->has('error_description')) {
            throw new SocialSignInException(trans('errors.social_login_bad_response', [
                'socialAccount' => $socialDriver,
                'error'         => $request->get('error_description'),
            ]), '/login');
        }

        $action = session()->pull('social-callback');

        // Attempt login or fall-back to register if allowed.
        $socialUser = $this->socialAuthService->getSocialUser($socialDriver);
        if ($action === 'login') {
            try {
                return $this->socialAuthService->handleLoginCallback($socialDriver, $socialUser);
            } catch (SocialSignInAccountNotUsed $exception) {
                if ($this->socialAuthService->driverAutoRegisterEnabled($socialDriver)) {
                    return $this->socialRegisterCallback($socialDriver, $socialUser);
                }

                throw $exception;
            }
        }

        if ($action === 'register') {
            return $this->socialRegisterCallback($socialDriver, $socialUser);
        }

        return redirect()->back();
    }

    /**
     * Detach a social account from a user.
     */
    public function detach(string $socialDriver)
    {
        $this->socialAuthService->detachSocialAccount($socialDriver);
        session()->flash('success', trans('settings.users_social_disconnected', ['socialAccount' => Str::title($socialDriver)]));

        return redirect(user()->getEditUrl());
    }

    /**
     * Register a new user after a registration callback.
     *
     * @throws UserRegistrationException
     */
    protected function socialRegisterCallback(string $socialDriver, SocialUser $socialUser)
    {
        $socialUser = $this->socialAuthService->handleRegistrationCallback($socialDriver, $socialUser);
        $socialAccount = $this->socialAuthService->newSocialAccount($socialDriver, $socialUser);
        $emailVerified = $this->socialAuthService->driverAutoConfirmEmailEnabled($socialDriver);

        // Create an array of the user data to create a new user instance
        $userData = [
            'name'     => $socialUser->getName(),
            'email'    => $socialUser->getEmail(),
            'password' => Str::random(32),
        ];

        // Take name from email address if empty
        if (!$userData['name']) {
            $userData['name'] = explode('@', $userData['email'])[0];
        }

        $user = $this->registrationService->registerUser($userData, $socialAccount, $emailVerified);
        $this->showSuccessNotification(trans('auth.register_success'));
        $this->loginService->login($user, $socialDriver);

        return redirect('/');
    }
}
