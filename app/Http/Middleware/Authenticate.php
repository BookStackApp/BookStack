<?php

namespace BookStack\Http\Middleware;

use BookStack\Http\Request;
use Closure;

class Authenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!hasAppAccess()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(url('/login'));
            }
        }

        return $next($request);
    }
}
