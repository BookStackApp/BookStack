<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $shareToken = $request->get('share_token');
        if ($shareToken && $request->is('attachments/*')) {
            $shareLinkExists = \BookStack\Entities\Models\EntityShareLink::query()
                ->where('token', '=', $shareToken)
                ->exists();
            
            if ($shareLinkExists) {
                return $next($request);
            }
        }

        if (!user()->hasAppAccess()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(url('/login'));
        }

        return $next($request);
    }
}
