<?php

namespace BookStack;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        $tail = implode('/', array_map(
                'rawurlencode', (array) $this->formatParameters($extra))
        );

        $defaultRoot = $this->formatRoot($this->formatScheme($secure));

        list($path, $query) = $this->extractQueryString($path);

        return $this->formatWithBase(
                $defaultRoot, trim($path.'/'.$tail, '/')
            ).$query;
    }

    /**
     * Format the given URL segments into a single URL.
     *
     * @param  string  $defaultRoot
     * @param  string  $path
     * @return string
     */
    public function formatWithBase($defaultRoot, $path)
    {
        $isFullPath = strpos($path, 'http') === 0;
        $setBasePath = trim(config('app.url'), '/');

        if ($isFullPath) {
            return $path;
        }

        if (! empty($setBasePath)) {
            $defaultRoot = $setBasePath;
        }

        // TODO - Add mechanism to set path correctly for intended() and back() redirects
        // TODO - Update tests to align with new system
        // TODO - Clean up helpers and refactor their usage.

        return trim($defaultRoot. '/' .$path, '/');
    }

}