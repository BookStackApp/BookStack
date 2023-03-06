<?php

namespace Cli\Services;

use Phar;

class AppLocator
{
    public static function search(string $directory = ''): string
    {
        $directoriesToSearch = $directory ? [$directory] : [
            getcwd(),
            static::getCliDirectory(),
        ];

        foreach ($directoriesToSearch as $directory) {
            if ($directory && static::isProbablyAppDirectory($directory)) {
                return $directory;
            }
        }

        return '';
    }

    public static function require(string $directory = ''): string
    {
        $dir = static::search($directory);

        if (!$dir) {
            throw new \Exception('Could not find a valid BookStack installation');
        }

        return $dir;
    }

    protected static function getCliDirectory(): string
    {
        $scriptDir = dirname(__DIR__);
        if (str_starts_with($scriptDir, 'phar://')) {
            $scriptDir = dirname(Phar::running(false));
        }

        return dirname($scriptDir);
    }

    protected static function isProbablyAppDirectory(string $directory): bool
    {
        return file_exists($directory . DIRECTORY_SEPARATOR . 'version')
            && file_exists($directory . DIRECTORY_SEPARATOR . 'package.json');
    }
}
