<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class ApplyCspRules
{
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
        $nonce = Str::random(24);
        view()->share('cspNonce', $nonce);

        // TODO - Assess whether image/style/iframe CSP rules should be set
        // TODO - Extract nonce application to custom head content in a way that's cacheable.
        // TODO - Fix remaining CSP issues and test lots

        $response = $next($request);

        $this->setFrameAncestors($response);
        $this->setScriptSrc($response, $nonce);

        return $response;
    }

    /**
     * Sets CSP 'script-src' headers to restrict the forms of script that can
     * run on the page.
     */
    public function setScriptSrc(Response $response, string $nonce)
    {
        $parts = [
            '\'self\'',
            '\'nonce-' . $nonce . '\'',
            '\'strict-dynamic\'',
        ];
        $response->headers->set('Content-Security-Policy', 'script-src ' . implode(' ', $parts));
    }

    /**
     * Sets CSP "frame-ancestors" headers to restrict the hosts that BookStack can be
     * iframed within. Also adjusts the cookie samesite options so that cookies will
     * operate in the third-party context.
     */
    protected function setFrameAncestors(Response $response)
    {
        $iframeHosts = collect(explode(' ', config('app.iframe_hosts', '')))->filter();

        if ($iframeHosts->count() > 0) {
            config()->set('session.same_site', 'none');
        }

        $iframeHosts->prepend("'self'");
        $cspValue = 'frame-ancestors ' . $iframeHosts->join(' ');
        $response->headers->set('Content-Security-Policy', $cspValue);
    }
}
