<?php

namespace BookStack\Http\Middleware;

use BookStack\Exceptions\ApiAuthException;
use BookStack\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    use ChecksForEmailConfirmation;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate the token and it's users API access
        try {
            $this->ensureAuthorizedBySessionOrToken();
        } catch (UnauthorizedException $exception) {
            return $this->unauthorisedResponse($exception->getMessage(), $exception->getCode());
        }

        return $next($request);
    }

    /**
     * Ensure the current user can access authenticated API routes, either via existing session
     * authentication or via API Token authentication.
     *
     * @throws UnauthorizedException
     */
    protected function ensureAuthorizedBySessionOrToken(): void
    {
        // Return if the user is already found to be signed in via session-based auth.
        // This is to make it easy to browser the API via browser after just logging into the system.
        if (signedInUser() || session()->isStarted()) {
            $this->ensureEmailConfirmedIfRequested();
            if (!user()->can('access-api')) {
                throw new ApiAuthException(trans('errors.api_user_no_api_permission'), 403);
            }

            return;
        }

        // Set our api guard to be the default for this request lifecycle.
        auth()->shouldUse('api');

        // Validate the token and it's users API access
        auth()->authenticate();
        $this->ensureEmailConfirmedIfRequested();
    }

    /**
     * Provide a standard API unauthorised response.
     */
    protected function unauthorisedResponse(string $message, int $code)
    {
        return response()->json([
            'error' => [
                'code'    => $code,
                'message' => $message,
            ],
        ], $code);
    }
}
