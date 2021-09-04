<?php

namespace BookStack\Http\Middleware;

use BookStack\Util\CspService;
use Closure;
use Illuminate\Http\Request;

class ApplyCspRules
{

    /**
     * @var CspService
     */
    protected $cspService;

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

        $this->cspService->setFrameAncestors($response);
        $this->cspService->setScriptSrc($response);
        $this->cspService->setObjectSrc($response);
        $this->cspService->setBaseUri($response);

        return $response;
    }

}
