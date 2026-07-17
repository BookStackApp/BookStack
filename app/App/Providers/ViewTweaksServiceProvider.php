<?php

namespace BookStack\App\Providers;

use BookStack\Entities\BreadcrumbsViewComposer;
use BookStack\Util\DateFormatter;
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
        View::share('dates', $this->app->make(DateFormatter::class));

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo (new \BookStack\Util\SvgIcon($expression))->toHtml(); ?>";
        });
    }
}
