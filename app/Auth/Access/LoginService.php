<?php

namespace BookStack\Auth\Access;

use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
use BookStack\Facades\Activity;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;

class LoginService
{

    /**
     * Log the given user into the system.
     */
    public function login(User $user, string $method): void
    {
        auth()->login($user);
        Activity::add(ActivityType::AUTH_LOGIN, "{$method}; {$user->logDescriptor()}");
        Theme::dispatch(ThemeEvents::AUTH_LOGIN, $method, $user);

        // Authenticate on all session guards if a likely admin
        if ($user->can('users-manage') && $user->can('user-roles-manage')) {
            $guards = ['standard', 'ldap', 'saml2'];
            foreach ($guards as $guard) {
                auth($guard)->login($user);
            }
        }
    }


    /**
     * Attempt the login of a user using the given credentials.
     * Meant to mirror laravel's default guard 'attempt' method
     * but in a manner that always routes through our login system.
     */
    public function attempt(array $credentials, string $method, bool $remember = false): bool
    {
        $result = auth()->attempt($credentials, $remember);
        if ($result) {
            $user = auth()->user();
            auth()->logout();
            $this->login($user, $method);
        }

        return $result;
    }

}