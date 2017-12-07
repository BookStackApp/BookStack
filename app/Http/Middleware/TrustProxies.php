<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies;

    /**
     * The current proxy header mappings.
     *
     * @var array
     */
    protected $headers = [
        Request::HEADER_FORWARDED => 'FORWARDED',
        Request::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
        Request::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
        Request::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
        Request::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
    ];

    /**
     * Handle the request, Set the correct user-configured proxy information.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $setProxies = config('app.proxies');
        if ($setProxies !== '**' && $setProxies !== '*' && $setProxies !== '') {
            $setProxies = explode(',', $setProxies);
        }
        $this->proxies = $setProxies;

        return parent::handle($request, $next);
    }
}
