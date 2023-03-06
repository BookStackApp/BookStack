<?php

namespace Cli\Commands;

use Cli\Services\AppLocator;
use Cli\Services\ArtisanRunner;
use Cli\Services\ComposerLocator;
use Cli\Services\ProgramRunner;
use Cli\Services\RequirementsValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('update');
        $this->setDescription('Update an existing BookStack instance.');
        $this->addOption('app-directory', null, InputOption::VALUE_OPTIONAL, 'BookStack install directory to update', '');
    }

    /**
     * @throws CommandError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $appDir = AppLocator::require($input->getOption('app-directory'));
        $output->writeln("<info>Checking system requirements...</info>");
        RequirementsValidator::validate();

        $output->writeln("<info>Checking composer exists...</info>");
        $composerLocator = new ComposerLocator($appDir);
        $composer = $composerLocator->getProgram();
        if (!$composer->isFound()) {
            $output->writeln("<info>Composer does not exist, downloading a local copy...</info>");
            $composerLocator->download();
        }

        $output->writeln("<info>Fetching latest code via Git...</info>");
        $this->updateCodeUsingGit($appDir);

        $output->writeln("<info>Installing PHP dependencies via composer...</info>");
        $this->installComposerDependencies($composer, $appDir);

        $output->writeln("<info>Running database migrations...</info>");
        $artisan = (new ArtisanRunner($appDir));
        $artisan->run(['migrate', '--force']);

        $output->writeln("<info>Clearing app caches...</info>");
        $artisan->run(['cache:clear']);
        $artisan->run(['config:clear']);
        $artisan->run(['view:clear']);

        return Command::SUCCESS;
    }

    /**
     * @throws CommandError
     */
    protected function updateCodeUsingGit(string $appDir): void
    {
        $errors = (new ProgramRunner('git', '/usr/bin/git'))
            ->withTimeout(240)
            ->withIdleTimeout(15)
            ->runCapturingStdErr([
                '-C', $appDir,
                'pull', '-q', 'origin', 'release',
            ]);

        if ($errors) {
            throw new CommandError("Failed git pull with errors:\n" . $errors);
        }
    }

    /**
     * @throws CommandError
     */
    protected function installComposerDependencies(ProgramRunner $composer, string $appDir): void
    {
        $errors = $composer->runCapturingStdErr([
            'install',
            '--no-dev', '-n', '-q', '--no-progress',
            '-d', $appDir,
        ]);

        if ($errors) {
            throw new CommandError("Failed composer install with errors:\n" . $errors);
        }
    }
}
