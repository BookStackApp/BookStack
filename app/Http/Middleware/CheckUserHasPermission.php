<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserHasPermission
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
        if (!user()->can($permission)) {
            return $this->errorResponse($request);
        }

        return $next($request);
    }


    protected function errorResponse(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['error' => trans('errors.permissionJson')], 403);
        }

        session()->flash('error', trans('errors.permission'));
        return redirect('/');
    }
}
