<?php

namespace BookStack\App\Providers;

use BookStack\Theming\ThemeEvents;
use BookStack\Theming\ThemeService;
use BookStack\Theming\ThemeViews;
use Illuminate\Support\Facades\Blade;
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
        $viewFactory = $this->app->make('view');
        $themeViews = new ThemeViews($viewFactory->getFinder());

        // Use a custom include so that we can insert theme views before/after includes.
        // This is done, even if no theme is active, so that view caching does not create problems
        // when switching between themes or when switching a theme on/off.
        $viewFactory->share('__themeViews', $themeViews);
        Blade::directive('include', function ($expression) {
            return "<?php echo \$__themeViews->handleViewInclude({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>";
        });

        if (!$themeService->getTheme()) {
            return;
        }

        $themeService->loadModules();
        $themeService->readThemeActions();
        $themeService->dispatch(ThemeEvents::APP_BOOT, $this->app);

        $themeViews->registerViewPathsForTheme($themeService->getModules());
        $themeService->dispatch(ThemeEvents::THEME_REGISTER_VIEWS, $themeViews);
    }
}
