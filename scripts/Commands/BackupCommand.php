<?php

namespace Cli\Commands;

use Minicli\Command\CommandCall;
use RecursiveDirectoryIterator;
use SplFileInfo;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use ZipArchive;

final class BackupCommand
{
    public function __construct(
        protected string $appDir
    ) {
    }

    public function handle(CommandCall $input)
    {
        $handleDatabase = !$input->hasFlag('no-database');
        $handleUploads = !$input->hasFlag('no-uploads');
        $suggestedOutPath = $input->subcommand ?: '';

        // TODO - Validate DB vars
        // TODO - Backup themes directory, extra flag for no-themes
        // TODO - Backup the running phar? For easier direct restore...
        // TODO - Error handle each stage
        // TODO - Validate zip (and any other extensions required) are active.

        $zipOutFile = $this->buildZipFilePath($suggestedOutPath);
        $dumpTempFile = $this->createDatabaseDump();

        // Create a new ZIP file
        $zipTempFile = tempnam(sys_get_temp_dir(), 'bsbackup');
        $zip = new ZipArchive();
        $zip->open($zipTempFile, ZipArchive::CREATE);
        $zip->addFile($this->appDir . DIRECTORY_SEPARATOR . '.env', '.env');

        if ($handleDatabase) {
            $zip->addFile($dumpTempFile, 'db.sql');
        }

        if ($handleUploads) {
            $this->addUploadFoldersToZip($zip);
        }

        // Close off our zip and move it to the required location
        $zip->close();
        rename($zipTempFile, $zipOutFile);

        // Delete our temporary DB dump file
        unlink($dumpTempFile);

        // Announce end and display errors
        echo "Finished";
    }

    /**
     * Build a full zip path from the given suggestion, which may be empty,
     * a path to a folder, or a path to a file in relative or absolute form.
     */
    protected function buildZipFilePath(string $suggestedOutPath): string
    {
        $zipDir = getcwd() ?: $this->appDir;
        $zipName = "bookstack-backup-" . date('Y-m-d-His') . '.zip';

        if ($suggestedOutPath) {
            if (is_dir($suggestedOutPath)) {
                $zipDir = realpath($suggestedOutPath);
            } else if (is_dir(dirname($suggestedOutPath))) {
                $zipDir = realpath(dirname($suggestedOutPath));
                $zipName = basename($suggestedOutPath);
            } else {
                // TODO - Handle not found output
            }
        }

        $fullPath = $zipDir . DIRECTORY_SEPARATOR . $zipName;

        if (file_exists($fullPath)) {
            // TODO
        }

        return $fullPath;
    }

    /**
     * Add app-relative upload folders to the provided zip archive.
     * Will recursively go through all directories to add all files.
     */
    protected function addUploadFoldersToZip(ZipArchive $zip): void
    {
        $fileDirs = [
            $this->appDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' => 'public/uploads',
            $this->appDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' => 'storage/uploads',
        ];

        foreach ($fileDirs as $fullFileDir => $relativeFileDir) {
            $dirIter = new RecursiveDirectoryIterator($fullFileDir);
            $fileIter = new \RecursiveIteratorIterator($dirIter);
            /** @var SplFileInfo $file */
            foreach ($fileIter as $file) {
                if (!$file->isDir()) {
                    $zip->addFile($file->getPathname(), $relativeFileDir . '/' . $fileIter->getSubPathname());
                }
            }
        }
    }

    /**
     * Create a database dump and return the path to the dumped SQL output.
     */
    protected function createDatabaseDump(): string
    {
        $dbHost = ($_SERVER['DB_HOST'] ?? '');
        $dbUser = ($_SERVER['DB_USERNAME'] ?? '');
        $dbPass = ($_SERVER['DB_PASSWORD'] ?? '');
        $dbDatabase = ($_SERVER['DB_DATABASE'] ?? '');

        // Create a mysqldump for the BookStack database
        $executableFinder = new ExecutableFinder();
        $mysqldumpPath = $executableFinder->find('mysqldump');

        $process = new Process([
            $mysqldumpPath,
            '-h', $dbHost,
            '-u', $dbUser,
            '-p' . $dbPass,
            '--single-transaction',
            '--no-tablespaces',
            $dbDatabase,
        ]);
        $process->start();

        $errors = "";
        $dumpTempFile = tempnam(sys_get_temp_dir(), 'bsbackup');
        $dumpTempFileResource = fopen($dumpTempFile, 'w');
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                fwrite($dumpTempFileResource, $data);
            } else { // $process::ERR === $type
                $errors .= $data . "\n";
            }
        }
        fclose($dumpTempFileResource);

        // TODO - Throw errors if existing
        return $dumpTempFile;
    }
}
