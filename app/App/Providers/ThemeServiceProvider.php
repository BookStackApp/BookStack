<?php

namespace BookStack\App\Providers;

use BookStack\Theming\ThemeEvents;
use BookStack\Theming\ThemeService;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the ThemeService as a singleton
        $this->app->singleton(ThemeService::class, fn ($app) => new ThemeService());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Boot up the theme system
        $themeService = $this->app->make(ThemeService::class);
        $themeService->readThemeActions();
        $themeService->dispatch(ThemeEvents::APP_BOOT, $this->app);
    }
}
