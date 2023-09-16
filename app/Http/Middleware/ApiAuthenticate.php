<?php

namespace BookStack\Http\Middleware;

use BookStack\Exceptions\ApiAuthException;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @throws ApiAuthException
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate the token and it's users API access
        $this->ensureAuthorizedBySessionOrToken();

        return $next($request);
    }

    /**
     * Ensure the current user can access authenticated API routes, either via existing session
     * authentication or via API Token authentication.
     *
     * @throws ApiAuthException
     */
    protected function ensureAuthorizedBySessionOrToken(): void
    {
        // Return if the user is already found to be signed in via session-based auth.
        // This is to make it easy to browser the API via browser after just logging into the system.
        if (!user()->isGuest() || session()->isStarted()) {
            if (!$this->sessionUserHasApiAccess()) {
                throw new ApiAuthException(trans('errors.api_user_no_api_permission'), 403);
            }

            return;
        }

        // Set our api guard to be the default for this request lifecycle.
        auth()->shouldUse('api');

        // Validate the token and it's users API access
        auth()->authenticate();
    }

    /**
     * Check if the active session user has API access.
     */
    protected function sessionUserHasApiAccess(): bool
    {
        $hasApiPermission = user()->can('access-api');

        return $hasApiPermission && user()->hasAppAccess();
    }
}
