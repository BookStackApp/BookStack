<?php

namespace BookStack\Providers;

use BookStack\Actions\ActivityLogger;
use BookStack\Auth\Permissions\PermissionApplicator;
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
            return $this->app->make(ActivityLogger::class);
        });

        $this->app->singleton('theme', function () {
            return $this->app->make(ThemeService::class);
        });
    }
}
