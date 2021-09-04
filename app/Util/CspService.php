<?php

namespace BookStack\Util;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CspService
{
    /** @var string */
    protected $nonce;

    public function __construct(string $nonce = '')
    {
        $this->nonce = $nonce ?: Str::random(16);
    }

    /**
     * Get the nonce value for CSP.
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * Sets CSP 'script-src' headers to restrict the forms of script that can
     * run on the page.
     */
    public function setScriptSrc(Response $response)
    {
        if (config('app.allow_content_scripts')) {
            return;
        }

        $parts = [
            '\'nonce-' . $this->nonce . '\'',
            '\'strict-dynamic\'',
        ];
        $value = 'script-src ' . implode(' ', $parts);
        $response->headers->set('Content-Security-Policy', $value, false);
    }

    /**
     * Sets CSP "frame-ancestors" headers to restrict the hosts that BookStack can be
     * iframed within. Also adjusts the cookie samesite options so that cookies will
     * operate in the third-party context.
     */
    public function setFrameAncestors(Response $response)
    {
        $iframeHosts = $this->getAllowedIframeHosts();
        array_unshift($iframeHosts, "'self'");
        $cspValue = 'frame-ancestors ' . implode(' ', $iframeHosts);
        $response->headers->set('Content-Security-Policy', $cspValue, false);
    }

    /**
     * Check if the user has configured some allowed iframe hosts.
     */
    public function allowedIFrameHostsConfigured(): bool
    {
        return count($this->getAllowedIframeHosts()) > 0;
    }


    protected function getAllowedIframeHosts(): array
    {
        $hosts = config('app.iframe_hosts', '');
        return array_filter(explode(' ', $hosts));
    }

}