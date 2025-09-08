<?php

namespace BookStack\Http\Middleware;

use BookStack\Permissions\Permission;
use Closure;
use Illuminate\Http\Request;

class CheckUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string|Permission $permission)
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
