<?php

namespace BookStack\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        \BookStack\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \BookStack\Http\Middleware\TrimStrings::class,
        \BookStack\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \BookStack\Http\Middleware\ControlIframeSecurity::class,
            \BookStack\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \BookStack\Http\Middleware\VerifyCsrfToken::class,
            \BookStack\Http\Middleware\RunThemeActions::class,
            \BookStack\Http\Middleware\Localization::class,
        ],
        'api' => [
            \BookStack\Http\Middleware\ThrottleApiRequests::class,
            \BookStack\Http\Middleware\EncryptCookies::class,
            \BookStack\Http\Middleware\StartSessionIfCookieExists::class,
            \BookStack\Http\Middleware\ApiAuthenticate::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => \BookStack\Http\Middleware\Authenticate::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \BookStack\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'perm'       => \BookStack\Http\Middleware\PermissionMiddleware::class,
        'guard'      => \BookStack\Http\Middleware\CheckGuard::class,
    ];
}
