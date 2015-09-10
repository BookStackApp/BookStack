<?php

namespace BookStack\Providers;

use Illuminate\Support\ServiceProvider;
use BookStack\Services\ActivityService;
use BookStack\Services\SettingService;

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
            return new ActivityService($this->app->make('BookStack\Activity'));
        });

        $this->app->bind('setting', function() {
            return new SettingService(
                $this->app->make('BookStack\Setting'),
                $this->app->make('Illuminate\Contracts\Cache\Repository')
            );
        });
    }
}
