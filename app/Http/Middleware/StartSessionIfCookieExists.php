<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Session\Middleware\StartSession as Middleware;

class StartSessionIfCookieExists extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $sessionCookieName = config('session.cookie');
        if ($request->cookies->has($sessionCookieName)) {
            return parent::handle($request, $next);
        }

        return $next($request);
    }
}
