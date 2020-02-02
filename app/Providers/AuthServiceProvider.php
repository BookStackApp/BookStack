<?php

namespace BookStack\Providers;

use Auth;
use BookStack\Api\ApiTokenGuard;
use BookStack\Auth\Access\ExternalBaseUserProvider;
use BookStack\Auth\Access\Guards\LdapSessionGuard;
use BookStack\Auth\Access\Guards\Saml2SessionGuard;
use BookStack\Auth\Access\LdapService;
use BookStack\Auth\UserRepo;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('api-token', function ($app, $name, array $config) {
            return new ApiTokenGuard($app['request']);
        });

        Auth::extend('ldap-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            return new LdapSessionGuard(
                $name,
                $provider,
                $this->app['session.store'],
                $app[LdapService::class],
                $app[UserRepo::class]
            );
        });

        Auth::extend('saml2-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            return new Saml2SessionGuard(
                $name,
                $provider,
                $this->app['session.store'],
                $app[UserRepo::class]
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
