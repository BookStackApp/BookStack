<?php

namespace Cli\Commands;

use Minicli\Command\CommandCall;
use RecursiveDirectoryIterator;
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
        // TODO - Customizable output file
        // TODO - Database only command
        // TODO - Validate DB vars
        // TODO - Error handle each stage
        // TODO - Validate zip (and any other extensions required) are active.

        $zipOutFile = getcwd() . DIRECTORY_SEPARATOR . 'backup.zip';

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


        // Create a new ZIP file
        $zipTempFile = tempnam(sys_get_temp_dir(), 'bsbackup');
        $zip = new ZipArchive();
        $sep = DIRECTORY_SEPARATOR;
        $zip->open($zipTempFile, ZipArchive::CREATE);
        $zip->addFile($this->appDir . $sep . '.env', '.env');
        $zip->addFile($dumpTempFile, 'db.sql');

        $fileDirs = [
            $this->appDir . $sep . 'public' . $sep . 'uploads' => 'public/uploads',
            $this->appDir . $sep . 'storage' . $sep . 'uploads' => 'storage/uploads',
        ];

        foreach ($fileDirs as $fullFileDir => $relativeFileDir) {
            $dirIter = new RecursiveDirectoryIterator($fullFileDir);
            $fileIter = new \RecursiveIteratorIterator($dirIter);
            /** @var \SplFileInfo $file */
            foreach ($fileIter as $file) {
                if (!$file->isDir()) {
                    $zip->addFile($file->getPathname(), $relativeFileDir . '/' . $fileIter->getSubPathname());
                }
            }
        }

        // Close off our zip and move it to the required location
        $zip->close();
        rename($zipTempFile, $zipOutFile);

        // Delete our temporary DB dump file
        unlink($dumpTempFile);

        // Announce end and display errors
        echo "Finished";
        if ($errors) {
            echo " with the following errors:\n" . $errors;
        }
    }
}
