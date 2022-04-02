<?php

namespace BookStack\Http;

use Illuminate\Http\Request as LaravelRequest;

class Request extends LaravelRequest
{
    /**
     * Override the default request methods to get the scheme and host
     * to directly use the custom APP_URL, if set.
     *
     * @return string
     */
    public function getSchemeAndHttpHost()
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
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $appUrl = config('app.url', null);

        if ($appUrl) {
            return rtrim(implode('/', array_slice(explode('/', $appUrl), 3)), '/');
        }

        return parent::getBaseUrl();
    }
}
