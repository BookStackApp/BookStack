<?php

namespace BookStack\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class PreventResponseCaching
{
    /**
     * Paths to ignore when preventing response caching.
     */
    protected array $ignoredPathPrefixes = [
        'theme/',
    ];

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

        $path = $request->path();
        foreach ($this->ignoredPathPrefixes as $ignoredPath) {
            if (str_starts_with($path, $ignoredPath)) {
                return $response;
            }
        }

        $response->headers->set('Cache-Control', 'no-cache, no-store, private');
        $response->headers->set('Expires', 'Sun, 12 Jul 2015 19:01:00 GMT');

        return $response;
    }
}
