<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\EmailConfirmationService;
use BookStack\Auth\Access\LoginService;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\ConfirmationEmailException;
use BookStack\Exceptions\UserTokenExpiredException;
use BookStack\Exceptions\UserTokenNotFoundException;
use BookStack\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ConfirmEmailController extends Controller
{
    protected $emailConfirmationService;
    protected $loginService;
    protected $userRepo;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        EmailConfirmationService $emailConfirmationService,
        LoginService $loginService,
        UserRepo $userRepo
    ) {
        $this->emailConfirmationService = $emailConfirmationService;
        $this->loginService = $loginService;
        $this->userRepo = $userRepo;
    }

    /**
     * Show the page to tell the user to check their email
     * and confirm their address.
     */
    public function show()
    {
        return view('auth.register-confirm');
    }

    /**
     * Shows a notice that a user's email address has not been confirmed,
     * Also has the option to re-send the confirmation email.
     */
    public function showAwaiting()
    {
        $user = $this->loginService->getLastLoginAttemptUser();

        return view('auth.user-unconfirmed', ['user' => $user]);
    }

    /**
     * Confirms an email via a token and logs the user into the system.
     *
     * @throws ConfirmationEmailException
     * @throws Exception
     */
    public function confirm(string $token)
    {
        try {
            $userId = $this->emailConfirmationService->checkTokenAndGetUserId($token);
        } catch (UserTokenNotFoundException $exception) {
            $this->showErrorNotification(trans('errors.email_confirmation_invalid'));

            return redirect('/register');
        } catch (UserTokenExpiredException $exception) {
            $user = $this->userRepo->getById($exception->userId);
            $this->emailConfirmationService->sendConfirmation($user);
            $this->showErrorNotification(trans('errors.email_confirmation_expired'));

            return redirect('/register/confirm');
        }

        $user = $this->userRepo->getById($userId);
        $user->email_confirmed = true;
        $user->save();

        $this->emailConfirmationService->deleteByUser($user);
        $this->showSuccessNotification(trans('auth.email_confirm_success'));

        return redirect('/login');
    }

    /**
     * Resend the confirmation email.
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        $user = $this->userRepo->getByEmail($request->get('email'));

        try {
            $this->emailConfirmationService->sendConfirmation($user);
        } catch (Exception $e) {
            $this->showErrorNotification(trans('auth.email_confirm_send_error'));

            return redirect('/register/confirm');
        }

        $this->showSuccessNotification(trans('auth.email_confirm_resent'));

        return redirect('/register/confirm');
    }
}
