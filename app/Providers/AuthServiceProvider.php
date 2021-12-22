<?php

namespace BookStack\Providers;

use BookStack\Api\ApiTokenGuard;
use BookStack\Auth\Access\ExternalBaseUserProvider;
use BookStack\Auth\Access\Guards\AsyncExternalBaseSessionGuard;
use BookStack\Auth\Access\Guards\LdapSessionGuard;
use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Access\LoginService;
use BookStack\Auth\Access\RegistrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Password Configuration
        Password::defaults(function () {
            return Password::min(8);
        });

        // Custom guards
        Auth::extend('api-token', function ($app, $name, array $config) {
            return new ApiTokenGuard($app['request'], $app->make(LoginService::class));
        });

        Auth::extend('ldap-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new LdapSessionGuard(
                $name,
                $provider,
                $app['session.store'],
                $app[LdapService::class],
                $app[RegistrationService::class]
            );
        });

        Auth::extend('async-external-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new AsyncExternalBaseSessionGuard(
                $name,
                $provider,
                $app['session.store'],
                $app[RegistrationService::class]
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('external-users', function ($app, array $config) {
            return new ExternalBaseUserProvider($config['model']);
        });
    }
}
