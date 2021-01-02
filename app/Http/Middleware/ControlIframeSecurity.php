<?php

namespace BookStack\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets CSP headers to restrict the hosts that BookStack can be
 * iframed within. Also adjusts the cookie samesite options
 * so that cookies will operate in the third-party context.
 */
class ControlIframeSecurity
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
        $iframeHosts = collect(explode(' ', config('app.iframe_hosts', '')))->filter();
        if ($iframeHosts->count() > 0) {
            config()->set('session.same_site', 'none');
        }

        $iframeHosts->prepend("'self'");

        $response = $next($request);
        $cspValue = 'frame-ancestors ' . $iframeHosts->join(' ');
        $response->headers->set('Content-Security-Policy', $cspValue);
        return $response;
    }
}
