<?php

namespace BookStack\Http\Middleware;

use BookStack\Util\CspService;
use Closure;
use Illuminate\Http\Request;

class ApplyCspRules
{
    protected CspService $cspService;

    public function __construct(CspService $cspService)
    {
        $this->cspService = $cspService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        view()->share('cspNonce', $this->cspService->getNonce());
        if ($this->cspService->allowedIFrameHostsConfigured()) {
            config()->set('session.same_site', 'none');
        }

        $response = $next($request);

        $cspHeader = $this->cspService->getCspHeader();
        $response->headers->set('Content-Security-Policy', $cspHeader, false);

        return $response;
    }
}
