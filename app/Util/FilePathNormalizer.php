<?php

namespace BookStack\Util;

use League\Flysystem\WhitespacePathNormalizer;

/**
 * Utility to normalize (potentially) user provided file paths
 * to avoid things like directory traversal.
 */
class FilePathNormalizer
{
    public static function normalize(string $path): string
    {
        return (new WhitespacePathNormalizer())->normalizePath($path);
    }
}
