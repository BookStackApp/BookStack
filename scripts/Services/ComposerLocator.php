<?php

namespace Cli\Services;

use Exception;

class ComposerLocator
{
    public function __construct(
        protected string $appDir
    ) {
    }

    public function getProgram(): ProgramRunner
    {
        return (new ProgramRunner('composer', '/usr/local/bin/composer'))
            ->withTimeout(300)
            ->withIdleTimeout(15)
            ->withAdditionalPathLocation($this->appDir);
    }

    /**
     * @throws Exception
     */
    public function download(): void
    {
        $setupPath = $this->appDir . DIRECTORY_SEPARATOR . 'composer-setup.php';
        $signature = file_get_contents('https://composer.github.io/installer.sig');
        copy('https://getcomposer.org/installer', $setupPath);
        $checksum = hash_file('sha384', $setupPath);

        if ($signature !== $checksum) {
            unlink($setupPath);
            throw new Exception("Could not install composer, checksum validation failed.");
        }

        $status = (new ProgramRunner('php', '/usr/bin/php'))
            ->runWithoutOutputCallbacks([
                $setupPath, '--quiet',
                "--install-dir={$this->appDir}",
                "--filename=composer",
            ]);

        unlink($setupPath);

        if ($status !== 0) {
            throw new Exception("Could not install composer, composer-setup script run failed.");
        }
    }
}
