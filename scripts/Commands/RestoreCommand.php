<?php

namespace Cli\Commands;

use Cli\Services\AppLocator;
use Cli\Services\ArtisanRunner;
use Cli\Services\BackupZip;
use Cli\Services\EnvironmentLoader;
use Cli\Services\InteractiveConsole;
use Cli\Services\MySqlRunner;
use Cli\Services\ProgramRunner;
use Cli\Services\RequirementsValidator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
        (new ProgramRunner('mysql', '/usr/bin/mysql'))->ensureFound();

        $zipPath = realpath($input->getArgument('backup-zip'));
        $zip = new BackupZip($zipPath);
        // TODO - Fix folders not being picked up here:
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

        if ($contents['env']['exists']) {
            $output->writeln("<info>Restoring and merging .env file...</info>");
            $this->restoreEnv($extractDir, $appDir);
        }

        $folderLocations = ['themes', 'public/uploads', 'storage/uploads'];
        foreach ($folderLocations as $folderSubPath) {
            if ($contents[$folderSubPath]['exists']) {
                $output->writeln("<info>Restoring {$folderSubPath} folder...</info>");
                $this->restoreFolder($folderSubPath, $appDir, $extractDir);
            }
        }

        if ($contents['db']['exists']) {
            $output->writeln("<info>Restoring database from SQL dump...</info>");
            $this->restoreDatabase($appDir, $extractDir);

            $output->writeln("<info>Running database migrations...</info>");
            $artisan = (new ArtisanRunner($appDir));
            $artisan->run(['migrate', '--force']);
        }

        // TODO - Handle change of URL?
        // TODO - Update system URL (via BookStack artisan command) if
        //   there's been a change from old backup env

        $output->writeln("<info>Clearing app caches...</info>");
        $artisan->run(['cache:clear']);
        $artisan->run(['config:clear']);
        $artisan->run(['view:clear']);

        $output->writeln("<info>Cleaning up extract directory...</info>");
        $this->deleteDirectoryAndContents($extractDir);

        $output->writeln("<info>\nRestore operation complete!</info>");

        return Command::SUCCESS;
    }

    protected function restoreEnv(string $extractDir, string $appDir)
    {
        $oldEnv = EnvironmentLoader::load($extractDir);
        $currentEnv = EnvironmentLoader::load($appDir);
        $envContents = file_get_contents($extractDir . DIRECTORY_SEPARATOR . '.env');
        $appEnvPath = $appDir . DIRECTORY_SEPARATOR . '.env';

        $mysqlCurrent = MySqlRunner::fromEnvOptions($currentEnv);
        $mysqlOld = MySqlRunner::fromEnvOptions($oldEnv);
        if (!$mysqlOld->testConnection()) {
            $currentWorking = $mysqlCurrent->testConnection();
            if (!$currentWorking) {
                throw new CommandError("Could not find a working database configuration");
            }

            // Copy across new env details to old env
            $currentEnvContents = file_get_contents($appEnvPath);
            $currentEnvDbLines = array_values(array_filter(explode("\n", $currentEnvContents), function (string $line) {
                return str_starts_with($line, 'DB_');
            }));
            $oldEnvLines = array_values(array_filter(explode("\n", $currentEnvContents), function (string $line) {
                return !str_starts_with($line, 'DB_');
            }));
            $envContents = implode("\n", [
                '# Database credentials merged from existing .env file',
                ...$currentEnvDbLines,
                ...$oldEnvLines
            ]);
            copy($appEnvPath, $appEnvPath . '.backup');
        }

        file_put_contents($appDir . DIRECTORY_SEPARATOR . '.env', $envContents);
    }

    protected function restoreFolder(string $folderSubPath, string $appDir, string $extractDir): void
    {
        $fullAppFolderPath = $appDir . DIRECTORY_SEPARATOR . $folderSubPath;
        $this->deleteDirectoryAndContents($fullAppFolderPath);
        rename($extractDir . DIRECTORY_SEPARATOR . $folderSubPath, $fullAppFolderPath);
    }

    protected function deleteDirectoryAndContents(string $dir)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $path = $fileinfo->getRealPath();
            $fileinfo->isDir() ? rmdir($path) : unlink($path);
        }

        rmdir($dir);
    }

    protected function restoreDatabase(string $appDir, string $extractDir): void
    {
        $dbDump = $extractDir . DIRECTORY_SEPARATOR . 'db.sql';
        $currentEnv = EnvironmentLoader::load($appDir);
        $mysql = MySqlRunner::fromEnvOptions($currentEnv);
        $mysql->importSqlFile($dbDump);
    }
}
