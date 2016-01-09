<?php

namespace BookStack\Providers;

use Auth;
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
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('ldap', function($app, array $config) {
            return new LdapUserProvider($config['model']);
        });
    }
}
