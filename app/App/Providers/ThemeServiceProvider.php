<?php

namespace BookStack\App\Providers;

use BookStack\Theming\ThemeEvents;
use BookStack\Theming\ThemeService;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the ThemeService as a singleton
        $this->app->singleton(ThemeService::class, fn ($app) => new ThemeService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Boot up the theme system
        $themeService = $this->app->make(ThemeService::class);
        $themeService->readThemeActions();
        $themeService->dispatch(ThemeEvents::APP_BOOT, $this->app);
    }
}
