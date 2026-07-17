<?php

namespace BookStack\Util;

use BookStack\Exceptions\HttpFetchException;

/**
 * Validate the host we're connecting to when making a server-side-request.
 * Will use the given hosts config if given during construction otherwise
 * will look to the app configured config.
 *
 * The config format is a space-seperated list of URL prefixes which should contain the
 * protocol and host. It can optionally define a path prefix as part of the URL.
 * Wildcards, via a '*', can be used within these elements to match anything but a '/'.
 */
class SsrUrlValidator
{
    protected string $config;

    public function __construct(?string $config = null)
    {
        $this->config = $config ?? config('app.ssr_hosts') ?? '';
    }

    /**
     * @throws HttpFetchException
     */
    public function ensureAllowed(string $url): void
    {
        if (!$this->allowed($url)) {
            throw new HttpFetchException(trans('errors.http_ssr_url_no_match'));
        }
    }

    /**
     * Check if the given URL is allowed by the configured SSR host values.
     */
    public function allowed(string $url): bool
    {
        $allowed = $this->getHostPatterns();

        foreach ($allowed as $pattern) {
            if ($this->urlMatchesPattern($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function urlMatchesPattern($url, $pattern): bool
    {
        $pattern = rtrim(trim($pattern), '/');
        $url = trim($url);
        $urlParts = parse_url($url);

        if (empty($pattern) || empty($url) || $urlParts === false) {
            return false;
        }

        // Prevent potential tricks using percent encoded slashes
        if (str_contains(strtolower($urlParts['host'] ?? ''), '%2f')) {
            return false;
        }

        // Disregard query and fragment
        $url = explode('?', $url, 2)[0];
        $url = explode('#', $url, 2)[0];

        // Disregard userinfo if existing
        if (!empty($urlParts['user']) || !empty($urlParts['pass'])) {
            [$start, $postUserinfo] = explode('@', $url, 2);
            $preUserinfo = explode('//', $start, 2)[0];
            $url = ($preUserinfo ? $preUserinfo . '//' : '') . $postUserinfo;
        }

        // Prepare pattern
        $quoted = preg_quote($pattern, '/');
        $regexPattern = str_replace('\*', '[^\/]*', $quoted);

        // Check against our URL
        return preg_match('/^' . $regexPattern . '($|\/.*$)/i', $url);
    }

    /**
     * @return string[]
     */
    protected function getHostPatterns(): array
    {
        return explode(' ', strtolower($this->config));
    }
}
