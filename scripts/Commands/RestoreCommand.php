<?php

namespace Cli\Commands;

use Cli\Services\AppLocator;
use Cli\Services\ArtisanRunner;
use Cli\Services\BackupZip;
use Cli\Services\EnvironmentLoader;
use Cli\Services\InteractiveConsole;
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
        $interactions = new InteractiveConsole($this->getHelper('question'), $input, $output);

        $output->writeln("<info>Warning!</info>");
        $output->writeln("<info>- A restore operation will overwrite and remove files & content from an existing instance.</info>");
        $output->writeln("<info>- Any existing tables within the configured database will be dropped.</info>");
        $output->writeln("<info>- You should only restore into an instance of the same or newer BookStack version.</info>");
        $output->writeln("<info>- This command won't handle, restore or address any server configuration.</info>");

        $appDir = AppLocator::require($input->getOption('app-directory'));
        $output->writeln("<info>Checking system requirements...</info>");
        RequirementsValidator::validate();

        $zipPath = realpath($input->getArgument('backup-zip'));
        $zip = new BackupZip($zipPath);
        $contents = $zip->getContentsOverview();

        $output->writeln("\n<info>Contents found in the backup ZIP:</info>");
        $hasContent = false;
        foreach ($contents as $info) {
            $output->writeln(($info['exists'] ? '✔ ' : '❌ ') . $info['desc']);
            if ($info['exists']) {
                $hasContent = true;
            }
        }

        if (!$hasContent) {
            throw new CommandError("Provided ZIP backup [{$zipPath}] does not have any expected restore-able content.");
        }

        $output->writeln("<info>The checked elements will be restored into [{$appDir}].</info>");
        $output->writeln("<info>Existing content may be overwritten.</info>");
        $output->writeln("<info>Do you want to continue?</info>");

        if (!$interactions->confirm("Do you want to continue?")) {
            $output->writeln("<info>Stopping restore operation.</info>");
            return Command::SUCCESS;
        }

        $output->writeln("<info>Extracting ZIP into temporary directory...</info>");
        $extractDir = $appDir . DIRECTORY_SEPARATOR . 'restore-temp-' . time();
        if (!mkdir($extractDir)) {
            throw new CommandError("Could not create temporary extraction directory at [{$extractDir}].");
        }
        $zip->extractInto($extractDir);

        // TODO - Cleanup temp extract dir

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

    protected function restoreEnv(string $extractDir, string $appDir, InteractiveConsole $interactions)
    {
        $extractEnv = EnvironmentLoader::load($extractDir);
        $appEnv = EnvironmentLoader::load($appDir); // TODO - Probably pass in since we'll need the APP_URL later on.

        // TODO - Create mysql runner to take variables to a programrunner instance.
        //  Then test each, backup existing env, then replace env with old then overwrite
        //  db options if the new app env options are the valid ones.
    }
}
