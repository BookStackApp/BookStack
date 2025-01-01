<?php

namespace BookStack\App\Providers;

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->mapWebRoutes();
            $this->mapApiRoutes();
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function (Router $router) {
            require base_path('routes/web.php');
            Theme::dispatch(ThemeEvents::ROUTES_REGISTER_WEB, $router);
        });

        Route::group([
            'middleware' => ['web', 'auth'],
        ], function (Router $router) {
            Theme::dispatch(ThemeEvents::ROUTES_REGISTER_WEB_AUTH, $router);
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::group([
            'middleware' => 'api',
            'namespace'  => $this->namespace . '\Api',
            'prefix'     => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('exports', function (Request $request) {
            $user = user();
            $attempts = $user->isGuest() ? 4 : 10;
            $key = $user->isGuest() ? $request->ip() : $user->id;
            return Limit::perMinute($attempts)->by($key);
        });
    }
}
