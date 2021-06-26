<?php

namespace BookStack\Http\Middleware;

use Closure;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param                          $permission
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!$request->user() || !$request->user()->can($permission)) {
            session()->flash('error', trans('errors.permission'));

            return redirect()->back();
        }

        return $next($request);
    }
}
