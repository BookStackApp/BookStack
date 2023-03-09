<?php

namespace Cli\Commands;

use Cli\Services\ComposerLocator;
use Cli\Services\EnvironmentLoader;
use Cli\Services\ProgramRunner;
use Cli\Services\RequirementsValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('init');
        $this->setDescription('Initialise a new BookStack install. Does not configure the webserver or database.');
        $this->addArgument('target-directory', InputArgument::OPTIONAL, 'The directory to create the BookStack install within. Must be empty.', '');
    }

    /**
     * @throws CommandError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Checking system requirements...</info>");
        RequirementsValidator::validate();

        $suggestedOutPath = $input->getArgument('target-directory');

        $output->writeln("<info>Locating and checking install directory...</info>");
        $installDir = $this->getInstallDir($suggestedOutPath);
        $this->ensureInstallDirEmptyAndWritable($installDir);

        $output->writeln("<info>Cloning down BookStack project to install directory...</info>");
        $this->cloneBookStackViaGit($installDir);

        $output->writeln("<info>Checking composer exists...</info>");
        $composerLocator = new ComposerLocator($installDir);
        $composer = $composerLocator->getProgram();
        if (!$composer->isFound()) {
            $output->writeln("<info>Composer does not exist, downloading a local copy...</info>");
            $composerLocator->download();
        }

        $output->writeln("<info>Installing application dependencies using composer...</info>");
        $this->installComposerDependencies($composer, $installDir);

        $output->writeln("<info>Creating .env file from .env.example...</info>");
        copy($installDir . DIRECTORY_SEPARATOR . '.env.example', $installDir . DIRECTORY_SEPARATOR . '.env');
        sleep(1);

        $output->writeln("<info>Generating app key...</info>");
        $this->generateAppKey($installDir);

        // Announce end
        $output->writeln("<info>A BookStack install has been initialized at: {$installDir}\n</info>");
        $output->writeln("<info>You will still need to:</info>");
        $output->writeln("<info>- Update the .env file in the install with correct URL, database and email details.</info>");
        $output->writeln("<info>- Run 'php artisan migrate' to set-up the database.</info>");
        $output->writeln("<info>- Configure your webserver for use with BookStack.</info>");
        $output->writeln("<info>- Ensure the required directories (storage/ bootstrap/cache public/uploads) are web-server writable.</info>");

        return Command::SUCCESS;
    }

    protected function generateAppKey(string $installDir): void
    {
        $errors = (new ProgramRunner('php', '/usr/bin/php'))
            ->withTimeout(60)
            ->withIdleTimeout(5)
            ->withEnvironment(EnvironmentLoader::load($installDir))
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
    protected function installComposerDependencies(ProgramRunner $composer, string $installDir): void
    {
        $errors = $composer->runCapturingStdErr([
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
        $git = (new ProgramRunner('git', '/usr/bin/git'))
            ->withTimeout(240)
            ->withIdleTimeout(15);

        $errors = $git->runCapturingStdErr([
                'clone', '-q',
                '--branch', 'release',
                '--single-branch',
                'https://github.com/BookStackApp/BookStack.git',
                $installDir
            ]);

        if ($errors) {
            throw new CommandError("Failed git clone with errors:\n" . $errors);
        }

        // Disable file permission tracking for git repo
        $git->runCapturingStdErr([
            '-C', $installDir,
            'config', 'core.fileMode', 'false'
        ]);
    }

    /**
     * Ensure that the installation directory is completely empty to avoid potential conflicts or issues.
     * @throws CommandError
     */
    protected function ensureInstallDirEmptyAndWritable(string $installDir): void
    {
        $contents = array_diff(scandir($installDir), ['..', '.']);
        if (count($contents) > 0) {
            throw new CommandError("Expected install directory to be empty but existing files found in [{$installDir}] target location.");
        }

        if (!is_writable($installDir)) {
            throw new CommandError("Target install directory [{$installDir}] is not writable.");
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
                $dir = realpath($suggestedDir);
            } else {
                throw new CommandError("Could not resolve provided [{$suggestedDir}] path to an existing folder.");
            }
        }

        return $dir;
    }
}
