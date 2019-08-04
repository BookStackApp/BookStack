<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            $requireConfirmation = (setting('registration-confirmation') || setting('registration-restrict'));
            if ($requireConfirmation && !$this->auth->user()->email_confirmed) {
                return redirect('/register/confirm/awaiting');
            }
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
