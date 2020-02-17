<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\RegistrationService;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Exceptions\SocialDriverNotConfigured;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use BookStack\Exceptions\SocialSignInException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;

class SocialController extends Controller
{

    protected $socialAuthService;
    protected $registrationService;

    /**
     * SocialController constructor.
     */
    public function __construct(SocialAuthService $socialAuthService, RegistrationService $registrationService)
    {
        $this->middleware('guest')->only(['getRegister', 'postRegister']);
        $this->socialAuthService = $socialAuthService;
        $this->registrationService = $registrationService;
    }


    /**
     * Redirect to the relevant social site.
     * @throws \BookStack\Exceptions\SocialDriverNotConfigured
     */
    public function getSocialLogin(string $socialDriver)
    {
        session()->put('social-callback', 'login');
        return $this->socialAuthService->startLogIn($socialDriver);
    }

    /**
     * Redirect to the social site for authentication intended to register.
     * @throws SocialDriverNotConfigured
     * @throws UserRegistrationException
     */
    public function socialRegister(string $socialDriver)
    {
        $this->registrationService->ensureRegistrationAllowed();
        session()->put('social-callback', 'register');
        return $this->socialAuthService->startRegister($socialDriver);
    }

    /**
     * The callback for social login services.
     * @throws SocialSignInException
     * @throws SocialDriverNotConfigured
     * @throws UserRegistrationException
     */
    public function socialCallback(Request $request, string $socialDriver)
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
    public function detachSocialAccount(string $socialDriver)
    {
        $this->socialAuthService->detachSocialAccount($socialDriver);
        session()->flash('success', trans('settings.users_social_disconnected', ['socialAccount' => Str::title($socialDriver)]));
        return redirect(user()->getEditUrl());
    }

    /**
     * Register a new user after a registration callback.
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
            'password' => Str::random(32)
        ];

        $user = $this->registrationService->registerUser($userData, $socialAccount, $emailVerified);
        auth()->login($user);

        $this->showSuccessNotification(trans('auth.register_success'));
        return redirect('/');
    }
}
