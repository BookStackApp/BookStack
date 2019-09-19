<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\EmailConfirmationService;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\ConfirmationEmailException;
use BookStack\Exceptions\UserTokenExpiredException;
use BookStack\Exceptions\UserTokenNotFoundException;
use BookStack\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class ConfirmEmailController extends Controller
{
    protected $emailConfirmationService;
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     * @param EmailConfirmationService $emailConfirmationService
     * @param UserRepo $userRepo
     */
    public function __construct(EmailConfirmationService $emailConfirmationService, UserRepo $userRepo)
    {
        $this->emailConfirmationService = $emailConfirmationService;
        $this->userRepo = $userRepo;
        parent::__construct();
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
     * @return View
     */
    public function showAwaiting()
    {
        return view('auth.user-unconfirmed');
    }

    /**
     * Confirms an email via a token and logs the user into the system.
     * @param $token
     * @return RedirectResponse|Redirector
     * @throws ConfirmationEmailException
     * @throws Exception
     */
    public function confirm($token)
    {
        try {
            $userId = $this->emailConfirmationService->checkTokenAndGetUserId($token);
        } catch (Exception $exception) {
            if ($exception instanceof UserTokenNotFoundException) {
                $this->showErrorNotification( trans('errors.email_confirmation_invalid'));
                return redirect('/register');
            }

            if ($exception instanceof UserTokenExpiredException) {
                $user = $this->userRepo->getById($exception->userId);
                $this->emailConfirmationService->sendConfirmation($user);
                $this->showErrorNotification( trans('errors.email_confirmation_expired'));
                return redirect('/register/confirm');
            }

            throw $exception;
        }

        $user = $this->userRepo->getById($userId);
        $user->email_confirmed = true;
        $user->save();

        auth()->login($user);
        $this->showSuccessNotification( trans('auth.email_confirm_success'));
        $this->emailConfirmationService->deleteByUser($user);

        return redirect('/');
    }


    /**
     * Resend the confirmation email
     * @param Request $request
     * @return View
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email'
        ]);
        $user = $this->userRepo->getByEmail($request->get('email'));

        try {
            $this->emailConfirmationService->sendConfirmation($user);
        } catch (Exception $e) {
            $this->showErrorNotification( trans('auth.email_confirm_send_error'));
            return redirect('/register/confirm');
        }

        $this->showSuccessNotification( trans('auth.email_confirm_resent'));
        return redirect('/register/confirm');
    }
}
