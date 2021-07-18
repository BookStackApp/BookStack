<?php

namespace BookStack\Exceptions;

use BookStack\Auth\Access\LoginService;
use BookStack\Auth\User;
use Illuminate\Contracts\Support\Responsable;

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
     * @inheritdoc
     */
    public function toResponse($request)
    {
        $redirect = '/login';

        if ($this->loginService->awaitingEmailConfirmation($this->user)) {
            $redirect = '/register/confirm/awaiting';
        } else if  ($this->loginService->needsMfaVerification($this->user)) {
            $redirect = '/mfa/verify';
        }

        return redirect($redirect);
    }
}