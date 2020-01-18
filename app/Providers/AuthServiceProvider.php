<?php

namespace BookStack\Providers;

use Auth;
use BookStack\Api\ApiTokenGuard;
use BookStack\Auth\Access\LdapService;
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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('ldap', function ($app, array $config) {
            return new LdapUserProvider($config['model'], $app[LdapService::class]);
        });
    }
}
