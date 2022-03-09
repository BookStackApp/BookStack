<?php

namespace BookStack\Util;

use Illuminate\Support\Str;

class CspService
{
    protected string $nonce;

    public function __construct(string $nonce = '')
    {
        $this->nonce = $nonce ?: Str::random(24);
    }

    /**
     * Get the nonce value for CSP.
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * Get the CSP headers for the application.
     */
    public function getCspHeader(): string
    {
        $headers = [
            $this->getFrameAncestors(),
            $this->getFrameSrc(),
            $this->getScriptSrc(),
            $this->getObjectSrc(),
            $this->getBaseUri(),
        ];

        return implode('; ', array_filter($headers));
    }

    /**
     * Get the CSP rules for the application for a HTML meta tag.
     */
    public function getCspMetaTagValue(): string
    {
        $headers = [
            $this->getFrameSrc(),
            $this->getScriptSrc(),
            $this->getObjectSrc(),
            $this->getBaseUri(),
        ];

        return implode('; ', array_filter($headers));
    }

    /**
     * Check if the user has configured some allowed iframe hosts.
     */
    public function allowedIFrameHostsConfigured(): bool
    {
        return count($this->getAllowedIframeHosts()) > 0;
    }

    /**
     * Create CSP 'script-src' rule to restrict the forms of script that can run on the page.
     */
    protected function getScriptSrc(): string
    {
        if (config('app.allow_content_scripts')) {
            return '';
        }

        $parts = [
            'http:',
            'https:',
            '\'nonce-' . $this->nonce . '\'',
            '\'strict-dynamic\'',
        ];

        return 'script-src ' . implode(' ', $parts);
    }

    /**
     * Create CSP "frame-ancestors" rule to restrict the hosts that BookStack can be iframed within.
     */
    protected function getFrameAncestors(): string
    {
        $iframeHosts = $this->getAllowedIframeHosts();
        array_unshift($iframeHosts, "'self'");

        return 'frame-ancestors ' . implode(' ', $iframeHosts);
    }

    /**
     * Creates CSP "frame-src" rule to restrict what hosts/sources can be loaded
     * within iframes to provide an allow-list-style approach to iframe content.
     */
    protected function getFrameSrc(): string
    {
        $iframeHosts = $this->getAllowedIframeSources();
        array_unshift($iframeHosts, "'self'");

        return 'frame-src ' . implode(' ', $iframeHosts);
    }

    /**
     * Creates CSP 'object-src' rule to restrict the types of dynamic content
     * that can be embedded on the page.
     */
    protected function getObjectSrc(): string
    {
        if (config('app.allow_content_scripts')) {
            return '';
        }

        return "object-src 'self'";
    }

    /**
     * Creates CSP 'base-uri' rule to restrict what base tags can be set on
     * the page to prevent manipulation of relative links.
     */
    protected function getBaseUri(): string
    {
        return "base-uri 'self'";
    }

    protected function getAllowedIframeHosts(): array
    {
        $hosts = config('app.iframe_hosts', '');

        return array_filter(explode(' ', $hosts));
    }

    protected function getAllowedIframeSources(): array
    {
        $sources = config('app.iframe_sources', '');
        $hosts = array_filter(explode(' ', $sources));

        // Extract drawing service url to allow embedding if active
        $drawioConfigValue = config('services.drawio');
        if ($drawioConfigValue) {
            $drawioSource = is_string($drawioConfigValue) ? $drawioConfigValue : 'https://embed.diagrams.net/';
            $drawioSourceParsed = parse_url($drawioSource);
            $drawioHost = $drawioSourceParsed['scheme'] . '://' . $drawioSourceParsed['host'];
            $hosts[] = $drawioHost;
        }

        return $hosts;
    }
}
