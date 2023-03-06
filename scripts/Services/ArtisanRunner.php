<?php

namespace Cli\Services;

use Exception;

class ArtisanRunner
{
    public function __construct(
        protected string $appDir
    ) {
    }

    public function run(array $commandArgs)
    {
        $errors = (new ProgramRunner('php', '/usr/bin/php'))
            ->withTimeout(60)
            ->withIdleTimeout(5)
            ->withEnvironment(EnvironmentLoader::load($this->appDir))
            ->runCapturingAllOutput([
                $this->appDir . DIRECTORY_SEPARATOR . 'artisan',
                '-n', '-q',
                ...$commandArgs
            ]);

        if ($errors) {
            $cmdString = implode(' ', $commandArgs);
            throw new Exception("Failed 'php artisan {$cmdString}' with errors:\n" . $errors);
        }
    }
}