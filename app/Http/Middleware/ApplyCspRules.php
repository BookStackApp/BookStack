<?php

namespace BookStack\Http\Middleware;

use BookStack\Util\CspService;
use Closure;
use Illuminate\Http\Request;

class ApplyCspRules
{
    public function __construct(
        protected CspService $cspService
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        view()->share('cspNonce', $this->cspService->getNonce());
        if ($this->cspService->allowedIFrameHostsConfigured() && !$request->is('api/*')) {
            config()->set('session.same_site', 'none');
        }

        $response = $next($request);

        $cspHeader = $this->cspService->getCspHeader();
        $response->headers->set('Content-Security-Policy', $cspHeader, false);

        return $response;
    }
}
