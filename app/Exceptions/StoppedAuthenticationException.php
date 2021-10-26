<?php

namespace BookStack\Exceptions;

use BookStack\Auth\Access\LoginService;
use BookStack\Auth\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class StoppedAuthenticationException extends \Exception implements Responsable
{
    protected $user;
    protected $loginService;

    /**
     * StoppedAuthenticationException constructor.
     */
    public function __construct(User $user, LoginService $loginService)
    {
        $this->user = $user;
        $this->loginService = $loginService;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $redirect = '/login';

        if ($this->loginService->awaitingEmailConfirmation($this->user)) {
            return $this->awaitingEmailConfirmationResponse($request);
        }

        if ($this->loginService->needsMfaVerification($this->user)) {
            $redirect = '/mfa/verify';
        }

        return redirect($redirect);
    }

    /**
     * Provide an error response for when the current user's email is not confirmed
     * in a system which requires it.
     */
    protected function awaitingEmailConfirmationResponse(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'error' => [
                    'code'    => 401,
                    'message' => trans('errors.email_confirmation_awaiting'),
                ],
            ], 401);
        }

        if (session()->pull('sent-email-confirmation') === true) {
            return redirect('/register/confirm');
        }

        return redirect('/register/confirm/awaiting');
    }
}
