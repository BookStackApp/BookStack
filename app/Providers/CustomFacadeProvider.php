<?php

namespace BookStack\Providers;

use BookStack\Actions\ActivityService;
use BookStack\Actions\ViewService;
use BookStack\Settings\SettingService;
use BookStack\Uploads\ImageService;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('activity', function () {
            return $this->app->make(ActivityService::class);
        });

        $this->app->bind('views', function () {
            return $this->app->make(ViewService::class);
        });

        $this->app->bind('setting', function () {
            return $this->app->make(SettingService::class);
        });

        $this->app->bind('images', function () {
            return $this->app->make(ImageService::class);
        });
    }
}
