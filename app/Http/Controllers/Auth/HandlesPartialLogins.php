<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\LoginService;
use BookStack\Auth\User;
use BookStack\Exceptions\NotFoundException;

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
            throw new NotFoundException('A user for this action could not be found');
        }

        return $user;
    }
}
