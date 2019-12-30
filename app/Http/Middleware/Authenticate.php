<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    use ChecksForEmailConfirmation;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->awaitingEmailConfirmation()) {
            return $this->emailConfirmationErrorResponse($request);
        }

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
