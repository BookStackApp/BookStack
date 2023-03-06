<?php

namespace Cli\Commands;

use Cli\Services\AppLocator;
use Cli\Services\EnvironmentLoader;
use Cli\Services\ProgramRunner;
use RecursiveDirectoryIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use ZipArchive;

final class BackupCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('backup');
        $this->setDescription('Backup a BookStack installation to a single compressed ZIP file.');
        $this->addArgument('backup-path', InputArgument::OPTIONAL, 'Outfile file or directory to store the resulting backup file.', '');
        $this->addOption('no-database', null, null, "Skip adding a database dump to the backup");
        $this->addOption('no-uploads', null, null, "Skip adding uploaded files to the backup");
        $this->addOption('no-themes', null, null, "Skip adding the themes folder to the backup");
    }

    /**
     * @throws CommandError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $appDir = AppLocator::require($input->getOption('app-directory'));
        $output->writeln("<info>Checking system requirements...</info>");
        $this->ensureRequiredExtensionInstalled();

        $handleDatabase = !$input->getOption('no-database');
        $handleUploads = !$input->getOption('no-uploads');
        $handleThemes = !$input->getOption('no-themes');
        $suggestedOutPath = $input->getArgument('backup-path');

        $zipOutFile = $this->buildZipFilePath($suggestedOutPath, $appDir);

        // Create a new ZIP file
        $zipTempFile = tempnam(sys_get_temp_dir(), 'bsbackup');
        $dumpTempFile = '';
        $zip = new ZipArchive();
        $zip->open($zipTempFile, ZipArchive::CREATE);

        // Add default files (.env config file and this CLI)
        $zip->addFile($appDir . DIRECTORY_SEPARATOR . '.env', '.env');
        $zip->addFile($appDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'run', 'run');

        if ($handleDatabase) {
            $output->writeln("<info>Dumping the database via mysqldump...</info>");
            $dumpTempFile = $this->createDatabaseDump($appDir);
            $output->writeln("<info>Adding database dump to backup archive...</info>");
            $zip->addFile($dumpTempFile, 'db.sql');
        }

        if ($handleUploads) {
            $output->writeln("<info>Adding BookStack upload folders to backup archive...</info>");
            $this->addUploadFoldersToZip($zip, $appDir);
        }

        if ($handleThemes) {
            $output->writeln("<info>Adding BookStack theme folders to backup archive...</info>");
            $this->addFolderToZipRecursive($zip, implode(DIRECTORY_SEPARATOR, [$appDir, 'themes']), 'themes');
        }

        // Close off our zip and move it to the required location
        $zip->close();
        // Delete our temporary DB dump file if exists. Must be done after zip close.
        if ($dumpTempFile) {
            unlink($dumpTempFile);
        }
        // Move the zip into the target location
        rename($zipTempFile, $zipOutFile);

        // Announce end
        $output->writeln("<info>Backup finished.</info>");
        $output->writeln("Output ZIP saved to: {$zipOutFile}");

        return Command::SUCCESS;
    }

    /**
     * Ensure the required PHP extensions are installed for this command.
     * @throws CommandError
     */
    protected function ensureRequiredExtensionInstalled(): void
    {
        if (!extension_loaded('zip')) {
            throw new CommandError('The "zip" PHP extension is required to run this command');
        }
    }

    /**
     * Build a full zip path from the given suggestion, which may be empty,
     * a path to a folder, or a path to a file in relative or absolute form.
     * @throws CommandError
     */
    protected function buildZipFilePath(string $suggestedOutPath, string $appDir): string
    {
        $zipDir = getcwd() ?: $appDir;
        $zipName = "bookstack-backup-" . date('Y-m-d-His') . '.zip';

        if ($suggestedOutPath) {
            if (is_dir($suggestedOutPath)) {
                $zipDir = realpath($suggestedOutPath);
            } else if (is_dir(dirname($suggestedOutPath))) {
                $zipDir = realpath(dirname($suggestedOutPath));
                $zipName = basename($suggestedOutPath);
            } else {
                throw new CommandError("Could not resolve provided [{$suggestedOutPath}] path to an existing folder.");
            }
        }

        $fullPath = $zipDir . DIRECTORY_SEPARATOR . $zipName;

        if (file_exists($fullPath)) {
            throw new CommandError("Target ZIP output location at [{$fullPath}] already exists.");
        }

        return $fullPath;
    }

    /**
     * Add app-relative upload folders to the provided zip archive.
     * Will recursively go through all directories to add all files.
     */
    protected function addUploadFoldersToZip(ZipArchive $zip, string $appDir): void
    {
        $this->addFolderToZipRecursive($zip, implode(DIRECTORY_SEPARATOR, [$appDir, 'public', 'uploads']), 'public/uploads');
        $this->addFolderToZipRecursive($zip, implode(DIRECTORY_SEPARATOR, [$appDir, 'storage', 'uploads']), 'storage/uploads');
    }

    /**
     * Recursively add all contents of the given dirPath to the provided zip file
     * with a zip location of the targetZipPath.
     */
    protected function addFolderToZipRecursive(ZipArchive $zip, string $dirPath, string $targetZipPath): void
    {
        $dirIter = new RecursiveDirectoryIterator($dirPath);
        $fileIter = new \RecursiveIteratorIterator($dirIter);
        /** @var SplFileInfo $file */
        foreach ($fileIter as $file) {
            if (!$file->isDir()) {
                $zip->addFile($file->getPathname(), $targetZipPath . '/' . $fileIter->getSubPathname());
            }
        }
    }

    /**
     * Create a database dump and return the path to the dumped SQL output.
     * @throws CommandError
     */
    protected function createDatabaseDump(string $appDir): string
    {
        $envOptions = EnvironmentLoader::loadMergedWithCurrentEnv($appDir);
        $dbOptions = [
            'host' => ($envOptions['DB_HOST'] ?? ''),
            'username' => ($envOptions['DB_USERNAME'] ?? ''),
            'password' => ($envOptions['DB_PASSWORD'] ?? ''),
            'database' => ($envOptions['DB_DATABASE'] ?? ''),
        ];

        $port = $envOptions['DB_PORT'] ?? '';
        if ($port) {
            $dbOptions['host'] .= ':' . $port;
        }

        foreach ($dbOptions as $name => $option) {
            if (!$option) {
                throw new CommandError("Could not find a value for the database {$name}");
            }
        }

        $errors = "";
        $hasOutput = false;
        $dumpTempFile = tempnam(sys_get_temp_dir(), 'bsdbdump');
        $dumpTempFileResource = fopen($dumpTempFile, 'w');

        try {
            (new ProgramRunner('mysqldump', '/usr/bin/mysqldump'))
                ->withTimeout(240)
                ->withIdleTimeout(15)
                ->runWithoutOutputCallbacks([
                    '-h', $dbOptions['host'],
                    '-u', $dbOptions['username'],
                    '-p' . $dbOptions['password'],
                    '--single-transaction',
                    '--no-tablespaces',
                    $dbOptions['database'],
                ], function ($data) use (&$dumpTempFileResource, &$hasOutput) {
                    fwrite($dumpTempFileResource, $data);
                    $hasOutput = true;
                }, function ($error) use (&$errors) {
                    $errors .= $error . "\n";
                });
        } catch (\Exception $exception) {
            fclose($dumpTempFileResource);
            unlink($dumpTempFile);
            if ($exception instanceof ProcessTimedOutException) {
                if (!$hasOutput) {
                    throw new CommandError("mysqldump operation timed-out.\nNo data has been received so the connection to your database may have failed.");
                } else {
                    throw new CommandError("mysqldump operation timed-out after data was received.");
                }
            }
            throw new CommandError($exception->getMessage());
        }

        fclose($dumpTempFileResource);

        if ($errors) {
            unlink($dumpTempFile);
            throw new CommandError("Failed mysqldump with errors:\n" . $errors);
        }

        return $dumpTempFile;
    }
}
