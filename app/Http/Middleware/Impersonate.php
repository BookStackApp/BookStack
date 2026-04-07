<?php

namespace BookStack\Http\Middleware;

use BookStack\Permissions\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Impersonate
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $impersonateId = session('impersonate', null);
        if (empty($impersonateId)) {
            return $next($request);
        }

        $realUser = auth()->user();
        if ($realUser && $realUser->can(Permission::UsersManage)) {
            Auth::onceUsingId($impersonateId);
        }

        return $next($request);
    }
}
