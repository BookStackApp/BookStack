<?php

namespace BookStack\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class PreventResponseCaching
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, private');
        $response->headers->set('Expires', 'Sun, 12 Jul 2015 19:01:00 GMT');

        return $response;
    }
}
