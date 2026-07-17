<?php

declare(strict_types=1);

namespace BookStack\Util;

/**
 * Helps filter URLs to prevent use of undesired schemes.
 * Also parses and rebuilds the URL to ensure it's valid.
 */
class UrlFilter
{
    protected static array $allowedSchemes = ['http', 'https', 'mailto', 'tel', 'file', 'ftp', 'nntp', 'news'];
    protected string $url;

    public function __construct(string $url)
    {
        $this->url = trim($url);
    }

    /**
     * Check if the URL is allowed to be generally used as a link
     * in the application. This does not assure the original URL string
     * provided is safe as-is. Ensure you use the clean method to produce
     * a URL that is considered safe to use.
     */
    public function isAllowed(): bool
    {
        $urlParts = parse_url($this->url);
        if (!$urlParts) {
            return false;
        }

        // Extra check to help avoid scenarios where non-standard characters are used in the scheme
        // to work around parse_url handling with URLs which may be interpreted by the browser differently.
        if (str_contains($this->url, ':') && !preg_match('/^[a-z]+:/i', $this->url)) {
            return false;
        }

        if (isset($urlParts['scheme'])) {
            return in_array(strtolower($urlParts['scheme']), self::$allowedSchemes);
        }

        return true;
    }

    /**
     * Clean the URL to ensure it's valid and only uses the allowed schemes.
     * If the URL is not allowed, return a placeholder.
     */
    public function clean(): string
    {
        if (!$this->isAllowed()) {
            return '#badlink';
        }

        $urlParts = parse_url($this->url);
        if (!$urlParts) {
            return '#badlink';
        }

        $url = '';

        if (isset($urlParts['scheme']) || isset($urlParts['host'])) {
            $scheme = strtolower($urlParts['scheme'] ?? 'https');
            $url = $scheme . ':' . (isset($urlParts['host']) ? '//' : '');
        }

        if (isset($urlParts['user']) || isset($urlParts['pass'])) {
            $url .= $urlParts['user'] ?? '';
            if (isset($urlParts['pass'])) {
                $url .= ':' . $urlParts['pass'];
            }
            $url .= '@';
        }

        if (isset($urlParts['host'])) {
            $url .= $urlParts['host'];
        }
        if (isset($urlParts['port'])) {
            $url .= ':' . $urlParts['port'];
        }
        if (isset($urlParts['path'])) {
            $url .= $urlParts['path'];
        }
        if (isset($urlParts['query'])) {
            $url .= '?' . $urlParts['query'];
        }
        if (isset($urlParts['fragment'])) {
            $url .= '#' . $urlParts['fragment'];
        }

        return $url;
    }

    /**
     * Get schemes that are allowed to be used in content links.
     */
    public static function getAllowedSchemes(): array
    {
        return self::$allowedSchemes;
    }
}
