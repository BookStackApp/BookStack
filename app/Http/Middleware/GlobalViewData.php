<?php namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class GlobalViewData
 * Sets up data that is accessible to any view rendered by the web routes.
 */
class GlobalViewData
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        view()->share('signedIn', auth()->check());
        view()->share('currentUser', user());

        return $next($request);
    }
}
