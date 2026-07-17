<?php

namespace BookStack\Http\Middleware;

use BookStack\Exceptions\ApiAuthException;
use BookStack\Permissions\Permission;
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
        $this->ensureAuthorizedBySessionOrToken($request);

        return $next($request);
    }

    /**
     * Ensure the current user can access authenticated API routes, either via existing session
     * authentication or via API Token authentication.
     *
     * @throws ApiAuthException
     */
    protected function ensureAuthorizedBySessionOrToken(Request $request): void
    {
        // Use the active user session already exists.
        // This is to make it easy to explore API endpoints via the UI.
        if (session()->isStarted()) {
            // Ensure the user has API access permission
            if (!$this->sessionUserHasApiAccess()) {
                throw new ApiAuthException(trans('errors.api_user_no_api_permission'), 403);
            }

            // Only allow GET requests for cookie-based API usage
            if ($request->method() !== 'GET') {
                throw new ApiAuthException(trans('errors.api_cookie_auth_only_get'), 403);
            }

            return;
        }

        // Set our api guard to be the default for this request lifecycle.
        auth()->shouldUse('api');

        // Validate the token and its users API access
        auth()->authenticate();
    }

    /**
     * Check if the active session user has API access.
     */
    protected function sessionUserHasApiAccess(): bool
    {
        $hasApiPermission = user()->can(Permission::AccessApi);

        return $hasApiPermission && user()->hasAppAccess();
    }
}
