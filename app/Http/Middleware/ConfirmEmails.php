<?php

namespace BookStack\Http\Middleware;

use BookStack\Http\Request;
use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Confirms the current user's email address.
 * Must come after any middleware that may log users in.
 */
class ConfirmEmails
{
    /**
     * The Guard implementation.
     */
    protected $auth;

    /**
     * Create a new ConfirmEmails instance.
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->auth->check()) {
            $requireConfirmation = (setting('registration-confirmation') || setting('registration-restrict'));
            if ($requireConfirmation && !$this->auth->user()->email_confirmed) {
                return $this->errorResponse($request);
            }
        }

        return $next($request);
    }

    /**
     * Provide an error response for when the current user's email is not confirmed
     * in a system which requires it.
     */
    protected function errorResponse(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => trans('errors.email_confirmation_awaiting')
                ]
            ], 401);
        }

        return redirect('/register/confirm/awaiting');
    }
}
