<?php

namespace BookStack\Util;

class UrlComparison
{
    public function __construct(
        protected readonly string $a,
        protected readonly string $b,
    ) {
    }

    /**
     * Check if the two URLs have the same origin.
     */
    public function originsMatch(): bool
    {
        $aParts = parse_url($this->a);
        $bParts = parse_url($this->b);

        return ($aParts['host'] ?? '') === ($bParts['host'] ?? '')
            && ($aParts['scheme'] ?? '') === ($bParts['scheme'] ?? '')
            && ($aParts['port'] ?? '') === ($bParts['port'] ?? '');
    }

    /**
     * Check if there's some overlap between the two URLs' paths.
     */
    public function pathsOverlap(): bool
    {
        $aPath = parse_url($this->a, PHP_URL_PATH) ?? '';
        $bPath = parse_url($this->b, PHP_URL_PATH) ?? '';

        return str_starts_with($aPath, $bPath) || str_starts_with($bPath, $aPath);
    }
}
