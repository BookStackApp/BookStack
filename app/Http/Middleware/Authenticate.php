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
        if ($this->auth->check() && setting('registration-confirmation') && !$this->auth->user()->email_confirmed) {
            return redirect(baseUrl('/register/confirm/awaiting'));
        }

        if ($this->auth->guest() && !setting('app-public')) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(baseUrl('/login'));
            }
        }

        return $next($request);
    }
}
