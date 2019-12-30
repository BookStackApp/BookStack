<?php namespace BookStack\Http;

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
     * The priority ordering of middleware.
     */
    protected $middlewarePriority = [
        \BookStack\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \BookStack\Http\Middleware\StartSessionIfCookieExists::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \BookStack\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \BookStack\Http\Middleware\Localization::class,
        \BookStack\Http\Middleware\GlobalViewData::class,
        \BookStack\Http\Middleware\Authenticate::class,
        \BookStack\Http\Middleware\ApiAuthenticate::class,
        \BookStack\Http\Middleware\ConfirmEmails::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \BookStack\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \BookStack\Http\Middleware\VerifyCsrfToken::class,
            \BookStack\Http\Middleware\Localization::class,
            \BookStack\Http\Middleware\GlobalViewData::class,
            \BookStack\Http\Middleware\ConfirmEmails::class,
        ],
        'api' => [
            'throttle:60,1',
            \BookStack\Http\Middleware\StartSessionIfCookieExists::class,
            \BookStack\Http\Middleware\ApiAuthenticate::class,
            \BookStack\Http\Middleware\ConfirmEmails::class,
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
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'perm'       => \BookStack\Http\Middleware\PermissionMiddleware::class
    ];
}
