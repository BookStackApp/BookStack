<?php

namespace BookStack\Util;

use BookStack\Exceptions\HttpFetchException;

class SsrUrlValidator
{
    protected string $config;

    public function __construct(string $config = null)
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
        $pattern = trim($pattern);
        $url = trim($url);

        if (empty($pattern) || empty($url)) {
            return false;
        }

        $quoted = preg_quote($pattern, '/');
        $regexPattern = str_replace('\*', '.*', $quoted);

        return preg_match('/^' . $regexPattern . '.*$/i', $url);
    }

    /**
     * @return string[]
     */
    protected function getHostPatterns(): array
    {
        return explode(' ', strtolower($this->config));
    }
}
