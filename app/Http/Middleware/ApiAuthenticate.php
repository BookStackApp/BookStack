<?php

namespace BookStack\Http\Middleware;

use BookStack\Exceptions\ApiAuthException;
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
        // Return if the user is already found to be signed in via session-based auth.
        // This is to make it easy to browser the API via browser after just logging into the system.
        if (signedInUser()) {
            if ($this->awaitingEmailConfirmation()) {
                return $this->emailConfirmationErrorResponse($request);
            }
            return $next($request);
        }

        // Set our api guard to be the default for this request lifecycle.
        auth()->shouldUse('api');

        // Validate the token and it's users API access
        try {
            auth()->authenticate();
        } catch (ApiAuthException $exception) {
            return $this->unauthorisedResponse($exception->getMessage(), $exception->getCode());
        }

        if ($this->awaitingEmailConfirmation()) {
            return $this->emailConfirmationErrorResponse($request, true);
        }

        return $next($request);
    }

    /**
     * Provide a standard API unauthorised response.
     */
    protected function unauthorisedResponse(string $message, int $code)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ], 401);
    }
}
