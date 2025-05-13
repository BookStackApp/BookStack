<?php

namespace BookStack\App;

class AppVersion
{
    protected static string $version = '';

    /**
     * Get the application's version number from its top-level `version` text file.
     */
    public static function get(): string
    {
        if (!empty(static::$version)) {
            return static::$version;
        }

        $versionFile = base_path('version');
        $version = trim(file_get_contents($versionFile));
        static::$version = $version;

        return $version;
    }
}
