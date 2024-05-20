<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Exceptions\NotFoundException;
use BookStack\Users\Models\User;

trait HandlesPartialLogins
{
    /**
     * @throws NotFoundException
     */
    protected function currentOrLastAttemptedUser(): User
    {
        $loginService = app()->make(LoginService::class);
        $user = auth()->user() ?? $loginService->getLastLoginAttemptUser();

        if (!$user) {
            throw new NotFoundException(trans('errors.login_user_not_found'));
        }

        return $user;
    }
}
