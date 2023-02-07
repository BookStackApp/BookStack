<?php

namespace BookStack\Http;

use Illuminate\Http\Request as LaravelRequest;

class Request extends LaravelRequest
{
    /**
     * Override the default request methods to get the scheme and host
     * to directly use the custom APP_URL, if set.
     */
    public function getSchemeAndHttpHost(): string
    {
        $appUrl = config('app.url', null);

        if ($appUrl) {
            return implode('/', array_slice(explode('/', $appUrl), 0, 3));
        }

        return parent::getSchemeAndHttpHost();
    }

    /**
     * Override the default request methods to get the base URL
     * to directly use the custom APP_URL, if set.
     * The base URL never ends with a / but should start with one if not empty.
     */
    public function getBaseUrl(): string
    {
        $appUrl = config('app.url', null);

        if ($appUrl) {
            $parsedBaseUrl = rtrim(implode('/', array_slice(explode('/', $appUrl), 3)), '/');

            return empty($parsedBaseUrl) ? '' : ('/' . $parsedBaseUrl);
        }

        return parent::getBaseUrl();
    }
}
