<?php

namespace Cli\Commands;

use Cli\Services\ProgramRunner;
use Minicli\Command\CommandCall;

class InitCommand
{
    /**
     * @throws CommandError
     */
    public function handle(CommandCall $input)
    {
        $this->ensureRequiredExtensionInstalled(); // TODO - Ensure bookstack install deps are met?

        // TODO - Check composer and git exists before running
        // TODO - Look at better way of handling env usage, on demand maybe where needed?
        //   Env loading in main `run` script if confilicting with certain bits here (app key generate, hence APP_KEY overload)
        //   See dotenv's Dotenv::createArrayBacked as way to go this.
        //   (More of a change for 'backup' command).
        // TODO - Potentially download composer?

        $suggestedOutPath = $input->subcommand;
        if ($suggestedOutPath === 'default') {
            $suggestedOutPath = '';
        }

        echo "Locating and checking install directory...\n";
        $installDir = $this->getInstallDir($suggestedOutPath);
        $this->ensureInstallDirEmpty($installDir);

        echo "Cloning down BookStack project to install directory...\n";
        $this->cloneBookStackViaGit($installDir);

        echo "Installing application dependencies using composer...\n";
        $this->installComposerDependencies($installDir);

        echo "Creating .env file from .env.example...\n";
        copy($installDir . DIRECTORY_SEPARATOR . '.env.example', $installDir . DIRECTORY_SEPARATOR . '.env');
        sleep(1);

        echo "Generating app key...\n";
        $this->generateAppKey($installDir);

        // Announce end
        echo "A BookStack install has been initialized at: {$installDir}\n\n";
        echo "You will still need to:\n";
        echo "- Update the .env file in the install with correct URL, database and email details.\n";
        echo "- Run 'php artisan migrate' to set-up the database.\n";
        echo "- Configure your webserver for use with BookStack.\n";
        echo "- Ensure the required directories (storage/ bootstrap/cache public/uploads) are web-server writable.\n";
    }

    /**
     * Ensure the required PHP extensions are installed for this command.
     * @throws CommandError
     */
    protected function ensureRequiredExtensionInstalled(): void
    {
//        if (!extension_loaded('zip')) {
//            throw new CommandError('The "zip" PHP extension is required to run this command');
//        }
    }

    protected function generateAppKey(string $installDir): void
    {
        $errors = (new ProgramRunner('php', '/usr/bin/php'))
            ->withTimeout(60)
            ->withIdleTimeout(5)
            ->withEnvironment(['APP_KEY' => 'SomeRandomString'])
            ->runCapturingAllOutput([
                $installDir . DIRECTORY_SEPARATOR . 'artisan',
                'key:generate', '--force', '-n', '-q'
            ]);

        if ($errors) {
            throw new CommandError("Failed 'php artisan key:generate' with errors:\n" . $errors);
        }
    }

    /**
     * Run composer install to download PHP dependencies.
     * @throws CommandError
     */
    protected function installComposerDependencies(string $installDir): void
    {
        $errors = (new ProgramRunner('composer', '/usr/local/bin/composer'))
            ->withTimeout(300)
            ->withIdleTimeout(15)
            ->runCapturingStdErr([
                'install',
                '--no-dev', '-n', '-q', '--no-progress',
                '-d', $installDir
            ]);

        if ($errors) {
            throw new CommandError("Failed composer install with errors:\n" . $errors);
        }
    }

    /**
     * Clone a new instance of BookStack to the given install folder.
     * @throws CommandError
     */
    protected function cloneBookStackViaGit(string $installDir): void
    {
        $errors = (new ProgramRunner('git', '/usr/bin/git'))
            ->withTimeout(240)
            ->withIdleTimeout(15)
            ->runCapturingStdErr([
                'clone', '-q',
                '--branch', 'release',
                '--single-branch',
                'https://github.com/BookStackApp/BookStack.git',
                $installDir
            ]);

        if ($errors) {
            throw new CommandError("Failed git clone with errors:\n" . $errors);
        }
    }

    /**
     * Ensure that the installation directory is completely empty to avoid potential conflicts or issues.
     * @throws CommandError
     */
    protected function ensureInstallDirEmpty(string $installDir): void
    {
        $contents = array_diff(scandir($installDir), ['..', '.']);
        if (count($contents) > 0) {
            throw new CommandError("Expected install directory to be empty but existing files found in [{$installDir}] target location.");
        }
    }

    /**
     * Build a full path to the intended location for the BookStack install.
     * @throws CommandError
     */
    protected function getInstallDir(string $suggestedDir): string
    {
        $dir = getcwd();

        if ($suggestedDir) {
            if (is_file($suggestedDir)) {
                throw new CommandError("Was provided [{$suggestedDir}] as an install path but existing file provided.");
            } else if (is_dir($suggestedDir)) {
                $dir = realpath($suggestedDir);
            } else if (is_dir(dirname($suggestedDir))) {
                $created = mkdir($suggestedDir);
                if (!$created) {
                    throw new CommandError("Could not create directory [{$suggestedDir}] for install.");
                }
                $dir = $suggestedDir;
            } else {
                throw new CommandError("Could not resolve provided [{$suggestedDir}] path to an existing folder.");
            }
        }

        return $dir;
    }
}
