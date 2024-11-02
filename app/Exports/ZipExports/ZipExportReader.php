<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exceptions\ZipExportException;
use ZipArchive;

class ZipExportReader
{
    protected ZipArchive $zip;
    protected bool $open = false;

    public function __construct(
        protected string $zipPath,
    ) {
        $this->zip = new ZipArchive();
    }

    /**
     * @throws ZipExportException
     */
    protected function open(): void
    {
        if ($this->open) {
            return;
        }

        // Validate file exists
        if (!file_exists($this->zipPath) || !is_readable($this->zipPath)) {
            throw new ZipExportException(trans('errors.import_zip_cant_read'));
        }

        // Validate file is valid zip
        $opened = $this->zip->open($this->zipPath, ZipArchive::RDONLY);
        if ($opened !== true) {
            throw new ZipExportException(trans('errors.import_zip_cant_read'));
        }

        $this->open = true;
    }

    public function close(): void
    {
        if ($this->open) {
            $this->zip->close();
            $this->open = false;
        }
    }

    /**
     * @throws ZipExportException
     */
    public function readData(): array
    {
        $this->open();

        // Validate json data exists, including metadata
        $jsonData = $this->zip->getFromName('data.json') ?: '';
        $importData = json_decode($jsonData, true);
        if (!$importData) {
            throw new ZipExportException(trans('errors.import_zip_cant_decode_data'));
        }

        return $importData;
    }

    public function fileExists(string $fileName): bool
    {
        return $this->zip->statName("files/{$fileName}") !== false;
    }

    /**
     * @throws ZipExportException
     * @returns array{name: string, book_count: int, chapter_count: int, page_count: int}
     */
    public function getEntityInfo(): array
    {
        $data = $this->readData();
        $info = ['name' => '', 'book_count' => 0, 'chapter_count' => 0, 'page_count' => 0];

        if (isset($data['book'])) {
            $info['name'] = $data['book']['name'] ?? '';
            $info['book_count']++;
            $chapters = $data['book']['chapters'] ?? [];
            $pages = $data['book']['pages'] ?? [];
            $info['chapter_count'] += count($chapters);
            $info['page_count'] += count($pages);
            foreach ($chapters as $chapter) {
                $info['page_count'] += count($chapter['pages'] ?? []);
            }
        } elseif (isset($data['chapter'])) {
            $info['name'] = $data['chapter']['name'] ?? '';
            $info['chapter_count']++;
            $info['page_count'] += count($data['chapter']['pages'] ?? []);
        } elseif (isset($data['page'])) {
            $info['name'] = $data['page']['name'] ?? '';
            $info['page_count']++;
        }

        return $info;
    }
}
