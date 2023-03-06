<?php

namespace Cli\Commands;

use Cli\Services\AppLocator;
use Cli\Services\ArtisanRunner;
use Cli\Services\RequirementsValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RestoreCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('restore');
        $this->addArgument('backup-zip', InputArgument::REQUIRED, 'Path to the ZIP file containing your backup.');
        $this->setDescription('Restore data and files from a backup ZIP file.');
        $this->addOption('app-directory', null, InputOption::VALUE_OPTIONAL, 'BookStack install directory to restore into', '');
    }

    /**
     * @throws CommandError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $appDir = AppLocator::require($input->getOption('app-directory'));
        $output->writeln("<info>Checking system requirements...</info>");
        RequirementsValidator::validate();

        // TODO - Warn that potentially dangerous,
        //        warn for same/forward versions only,
        //        warn this won't handle server-level stuff

        // TODO - Validate provided backup zip contents
        //  - Display and prompt to user

        // TODO - Environment handling
        //  - Restore of old .env
        //  - Prompt for correct DB details (Test before serving?)
        //  - Prompt for correct URL (Allow entry of new?)

        // TODO - Restore folders from backup

        // TODO - Restore database from backup

        $output->writeln("<info>Running database migrations...</info>");
        $artisan = (new ArtisanRunner($appDir));
        $artisan->run(['migrate', '--force']);

        // TODO - Update system URL (via BookStack artisan command) if
        //   there's been a change from old backup env

        $output->writeln("<info>Clearing app caches...</info>");
        $artisan->run(['cache:clear']);
        $artisan->run(['config:clear']);
        $artisan->run(['view:clear']);

        return Command::SUCCESS;
    }
}
