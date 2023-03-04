<?php

namespace Cli\Services;

use Dotenv\Dotenv;

class EnvironmentLoader
{
    public static function load(string $rootPath): array
    {
        $dotenv = Dotenv::createArrayBacked($rootPath);
        return $dotenv->safeLoad();
    }

    public static function loadMergedWithCurrentEnv(string $rootPath): array
    {
        $env = static::load($rootPath);
        foreach ($_SERVER as $key => $val) {
            $env[$key] = $val;
        }
        return $env;
    }
}
