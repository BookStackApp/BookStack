<?php namespace BookStack\Providers;


use Illuminate\Pagination\PaginationServiceProvider as IlluminatePaginationServiceProvider;
use Illuminate\Pagination\Paginator;

class PaginationServiceProvider extends IlluminatePaginationServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Paginator::viewFactoryResolver(function () {
            return $this->app['view'];
        });

        Paginator::currentPathResolver(function () {
            return baseUrl($this->app['request']->path());
        });

        Paginator::currentPageResolver(function ($pageName = 'page') {
            $page = $this->app['request']->input($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return $page;
            }

            return 1;
        });
    }
}