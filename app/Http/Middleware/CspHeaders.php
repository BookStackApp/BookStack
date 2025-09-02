<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CspHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Generate nonce only once per request
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('cspNonce', $nonce);
        View::share('cspNonce', $nonce); // Share for Blade templates

        $response = $next($request);

        // Retrieve the same nonce (guaranteed match)
        $nonce = $request->attributes->get('cspNonce');

        $frameSrc = [
            "'self'",
            'https://*.youtube.com',
            'https://*.vimeo.com',
            'https://*.youtube-nocookie.com',
            'https://*.draw.io',
            'https://embed.diagrams.net',
            'https://www.loom.com',
        ];

        $csp = 'frame-src ' . implode(' ', $frameSrc) . '; ';
        $csp .= "script-src 'self' 'nonce-{$nonce}';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
