<?php

namespace BookStack\Providers;

use BookStack\Actions\ActivityService;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Theming\ThemeService;
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

        $this->app->singleton('images', function () {
            return $this->app->make(ImageService::class);
        });

        $this->app->singleton('permissions', function () {
            return $this->app->make(PermissionService::class);
        });

        $this->app->singleton('theme', function () {
            return $this->app->make(ThemeService::class);
        });
    }
}
