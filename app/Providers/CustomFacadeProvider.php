<?php

namespace BookStack\Providers;

use BookStack\Actions\Activity;
use BookStack\Actions\ActivityService;
use BookStack\Actions\View;
use BookStack\Actions\ViewService;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Settings\Setting;
use BookStack\Settings\SettingService;
use BookStack\Uploads\HttpFetcher;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageService;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;

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
            return new ActivityService(
                $this->app->make(Activity::class),
                $this->app->make(PermissionService::class)
            );
        });

        $this->app->bind('views', function () {
            return new ViewService(
                $this->app->make(View::class),
                $this->app->make(PermissionService::class)
            );
        });

        $this->app->bind('setting', function () {
            return new SettingService(
                $this->app->make(Setting::class),
                $this->app->make(Repository::class)
            );
        });

        $this->app->bind('images', function () {
            return new ImageService(
                $this->app->make(Image::class),
                $this->app->make(ImageManager::class),
                $this->app->make(Factory::class),
                $this->app->make(Repository::class),
                $this->app->make(HttpFetcher::class)
            );
        });
    }
}
