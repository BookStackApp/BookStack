<?php

namespace Oxbow\Providers;

use Illuminate\Support\ServiceProvider;
use Oxbow\Services\ActivityService;
use Oxbow\Services\SettingService;

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

        $this->app->bind('setting', function() {
            return new SettingService(
                $this->app->make('Oxbow\Setting'),
                $this->app->make('Illuminate\Contracts\Cache\Repository')
            );
        });
    }
}
