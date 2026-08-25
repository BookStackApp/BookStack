<?php

namespace BookStack\App\Providers;

use BookStack\Entities\BreadcrumbsViewComposer;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Util\DateFormatter;
use BookStack\View\ViewBlockManager;
use BookStack\View\ViewBlockPreferences;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewTweaksServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DateFormatter::class, function ($app) {
            return new DateFormatter(
                $app['config']->get('app.display_timezone'),
            );
        });

        $this->app->singleton(ViewBlockManager::class, function ($app) {
            return new ViewBlockManager(new ViewBlockPreferences());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set paginator to use bootstrap-style pagination
        Paginator::useBootstrap();

        // View Composers
        View::composer('entities.breadcrumbs', BreadcrumbsViewComposer::class);

        // View Globals
        $viewBlockManager = $this->app->make(ViewBlockManager::class);
        View::share('dates', $this->app->make(DateFormatter::class));
        View::share('viewBlocks', $viewBlockManager);
        Theme::dispatch(ThemeEvents::VIEW_BLOCKS_REGISTER, $viewBlockManager);

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo (new \BookStack\Util\SvgIcon($expression))->toHtml(); ?>";
        });
    }
}
