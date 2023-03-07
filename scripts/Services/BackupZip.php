<?php

namespace Cli\Services;

use ZipArchive;

class BackupZip
{
    protected ZipArchive $zip;
    public function __construct(
        protected string $filePath
    ) {
        $this->zip = new ZipArchive();
        $status = $this->zip->open($this->filePath);

        if (!file_exists($this->filePath) || $status !== true) {
            throw new \Exception("Could not open file [{$this->filePath}] as ZIP");
        }
    }

    /**
     * @return array<string, array{desc: string, exists: bool}>
     */
    public function getContentsOverview(): array
    {
        return [
            'env' => [
                'desc' => '.env Config File',
                'exists' => boolval($this->zip->statName('.env')),
            ],
            'themes' => [
                'desc' => 'Themes Folder',
                'exists' => $this->zip->locateName('/themes/') !== false,
            ],
            'public/uploads' => [
                'desc' => 'Public File Uploads',
                'exists' => $this->zip->locateName('/public/uploads/') !== false,
            ],
            'storage/uploads' => [
                'desc' => 'Private File Uploads',
                'exists' => $this->zip->locateName('/storage/uploads/') !== false,
            ],
            'db' => [
                'desc' => 'Database Dump',
                'exists' => boolval($this->zip->statName('db.sql')),
            ],
        ];
    }

    public function extractInto(string $directoryPath): void
    {
        $result = $this->zip->extractTo($directoryPath);
        if (!$result) {
            throw new \Exception("Failed extraction of ZIP into [{$directoryPath}].");
        }
    }
}
