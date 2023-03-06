<?php

namespace Cli\Commands;

use Cli\Services\ComposerLocator;
use Cli\Services\EnvironmentLoader;
use Cli\Services\ProgramRunner;
use Cli\Services\RequirementsValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{

    public function __construct(
        protected string $appDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('update');
        $this->setDescription('Update an existing BookStack instance.');
    }

    /**
     * @throws CommandError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Checking system requirements...</info>");
        RequirementsValidator::validate();

        $output->writeln("<info>Checking composer exists...</info>");
        $composerLocator = new ComposerLocator($this->appDir);
        $composer = $composerLocator->getProgram();
        if (!$composer->isFound()) {
            $output->writeln("<info>Composer does not exist, downloading a local copy...</info>");
            $composerLocator->download();
        }

        $output->writeln("<info>Fetching latest code via Git...</info>");
        $this->updateCodeUsingGit();

        $output->writeln("<info>Installing PHP dependencies via composer...</info>");
        $this->installComposerDependencies($composer);

        $output->writeln("<info>Running database migrations...</info>");
        $this->runArtisanCommand(['migrate', '--force']);

        $output->writeln("<info>Clearing app caches...</info>");
        $this->runArtisanCommand(['cache:clear']);
        $this->runArtisanCommand(['config:clear']);
        $this->runArtisanCommand(['view:clear']);

        return Command::SUCCESS;
    }

    /**
     * @throws CommandError
     */
    protected function updateCodeUsingGit(): void
    {
        $errors = (new ProgramRunner('git', '/usr/bin/git'))
            ->withTimeout(240)
            ->withIdleTimeout(15)
            ->runCapturingStdErr([
                '-C', $this->appDir,
                'pull', '-q', 'origin', 'release',
            ]);

        if ($errors) {
            throw new CommandError("Failed git pull with errors:\n" . $errors);
        }
    }

    /**
     * @throws CommandError
     */
    protected function installComposerDependencies(ProgramRunner $composer): void
    {
        $errors = $composer->runCapturingStdErr([
            'install',
            '--no-dev', '-n', '-q', '--no-progress',
            '-d', $this->appDir,
        ]);

        if ($errors) {
            throw new CommandError("Failed composer install with errors:\n" . $errors);
        }
    }

    protected function runArtisanCommand(array $commandArgs): void
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
            throw new CommandError("Failed 'php artisan {$cmdString}' with errors:\n" . $errors);
        }
    }
}
