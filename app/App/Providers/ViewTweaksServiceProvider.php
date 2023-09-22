<?php

namespace BookStack\App\Providers;

use BookStack\Entities\BreadcrumbsViewComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewTweaksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Set paginator to use bootstrap-style pagination
        Paginator::useBootstrap();

        // View Composers
        View::composer('entities.breadcrumbs', BreadcrumbsViewComposer::class);

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo (new \BookStack\Util\SvgIcon($expression))->toHtml(); ?>";
        });
    }
}
