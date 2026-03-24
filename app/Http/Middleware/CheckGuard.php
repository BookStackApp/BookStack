<?php

namespace BookStack\Http\Middleware;

use Closure;

class CheckGuard
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $allowedGuards
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$allowedGuards)
    {
        $enabledAllowedGuards = array_filter($allowedGuards, fn (string $guard) => auth_method_enabled($guard));
        if (count($enabledAllowedGuards) === 0) {
            session()->flash('error', trans('errors.permission'));

            return redirect('/');
        }

        return $next($request);
    }
}
