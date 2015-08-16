<?php

namespace Oxbow\Providers;

use Illuminate\Support\ServiceProvider;
use Oxbow\Services\ActivityService;

class CustomFacadeProvider extends ServiceProvider
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
        $this->app->bind('activity', function() {
            return new ActivityService($this->app->make('Oxbow\Activity'));
        });
    }
}
