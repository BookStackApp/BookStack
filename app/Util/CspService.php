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
            $this->getStyleSrc(),
            $this->getImgSrc(),
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
            $this->getStyleSrc(),
            $this->getImgSrc(),
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
        if ($this->scriptFilteringDisabled()) {
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
        if ($this->scriptFilteringDisabled()) {
            return '';
        }

        return "object-src 'self'";
    }

    /**
     * Creates CSP 'style-src' rule to restrict where styles can be loaded from.
     */
    protected function getStyleSrc(): string
    {
        return 'style-src ' . implode(' ', $this->getAllowedStyleSources());
    }

    /**
     * Creates CSP 'img-src' rule to restrict where images can be loaded from.
     */
    protected function getImgSrc(): string
    {
        return 'img-src ' . implode(' ', $this->getAllowedImageSources());
    }

    /**
     * Creates CSP 'base-uri' rule to restrict what base tags can be set on
     * the page to prevent manipulation of relative links.
     */
    protected function getBaseUri(): string
    {
        return "base-uri 'self'";
    }

    protected function scriptFilteringDisabled(): bool
    {
        return !HtmlContentFilterConfig::fromConfigString(config('app.content_filtering'))->filterOutJavaScript;
    }

    protected function getAllowedIframeHosts(): array
    {
        $hosts = config('app.iframe_hosts') ?? '';

        return array_filter(explode(' ', $hosts));
    }

    protected function getAllowedIframeSources(): array
    {
        $sources = explode(' ', config('app.iframe_sources', ''));
        $sources[] = $this->getDrawioHost();

        return array_filter($sources);
    }

    /**
     * Get allowed style sources for the style-src directive.
     */
    protected function getAllowedStyleSources(): array
    {
        $configured = config('app.style_sources');

        if (is_string($configured)) {
            $sources = array_filter(explode(' ', $configured));
            array_unshift($sources, "'self'");

            // Ensure 'unsafe-inline' is quoted if present
            // This is done as attempting to pass this in env values with quotes can either
            // be awkward or cause issues.
            $unsafeInlineIndex = array_search('unsafe-inline', $sources, true);
            if ($unsafeInlineIndex !== false) {
                $sources[$unsafeInlineIndex] = "'unsafe-inline'";
            }

            return array_values(array_unique($sources));
        }

        return [
            "'self'",
            "'unsafe-inline'",
            'http:',
            'https:',
        ];
    }

    /**
     * Get allowed image sources for the img-src directive.
     */
    protected function getAllowedImageSources(): array
    {
        $configured = config('app.image_sources');

        if (is_string($configured)) {
            $sources = array_filter(explode(' ', $configured));
            array_unshift($sources, "'self'", 'blob:', 'data:');

            return array_values(array_unique($sources));
        }

        return [
            "'self'",
            'data:',
            'blob:',
            'http:',
            'https:',
        ];
    }

    /**
     * Extract the host name of the configured drawio URL for use in CSP.
     * Returns empty string if not in use.
     */
    protected function getDrawioHost(): string
    {
        $drawioConfigValue = config('services.drawio');
        if (!$drawioConfigValue) {
            return '';
        }

        $drawioSource = is_string($drawioConfigValue) ? $drawioConfigValue : 'https://embed.diagrams.net/';
        $drawioSourceParsed = parse_url($drawioSource);
        $drawioHost = $drawioSourceParsed['scheme'] . '://' . $drawioSourceParsed['host'];
        if (isset($drawioSourceParsed['port'])) {
            $drawioHost .= ':' . $drawioSourceParsed['port'];
        }

        return $drawioHost;
    }
}
