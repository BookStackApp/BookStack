<?php

namespace BookStack\Providers;

use BookStack\Actions\ActivityService;
use BookStack\Actions\ViewService;
use BookStack\Auth\Permissions\PermissionService;
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
        $this->app->singleton('activity', function () {
            return $this->app->make(ActivityService::class);
        });

        $this->app->singleton('views', function () {
            return $this->app->make(ViewService::class);
        });

        $this->app->singleton('setting', function () {
            return $this->app->make(SettingService::class);
        });

        $this->app->singleton('images', function () {
            return $this->app->make(ImageService::class);
        });

        $this->app->singleton('permissions', function () {
            return $this->app->make(PermissionService::class);
        });
    }
}
