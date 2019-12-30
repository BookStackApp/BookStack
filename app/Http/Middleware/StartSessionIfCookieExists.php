<?php

namespace BookStack\Http\Middleware;

use BookStack\Http\Request;
use Closure;
use Exception;
use Illuminate\Session\Middleware\StartSession as Middleware;

class StartSessionIfCookieExists extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $sessionCookieName = config('session.cookie');
        if ($request->cookies->has($sessionCookieName)) {
            $this->decryptSessionCookie($request, $sessionCookieName);
            return parent::handle($request, $next);
        }

        return $next($request);
    }

    /**
     * Attempt decryption of the session cookie.
     */
    protected function decryptSessionCookie(Request $request, string $sessionCookieName)
    {
        try {
            $sessionCookie = $request->cookies->get($sessionCookieName);
            $sessionCookie = decrypt($sessionCookie, false);
            $request->cookies->set($sessionCookieName, $sessionCookie);
        } catch (Exception $e) {
            //
        }
    }
}
