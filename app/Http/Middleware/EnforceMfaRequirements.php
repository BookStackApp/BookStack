<?php

namespace BookStack\Http\Middleware;

use Closure;

class EnforceMfaRequirements
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $mfaRequired = user()->roles()->where('mfa_enforced', '=', true)->exists();
        // TODO - Run this after auth (If authenticated)
        // TODO - Redirect user to setup MFA or verify via MFA.
        // TODO - Store mfa_pass into session for future requests?
        return $next($request);
    }
}
