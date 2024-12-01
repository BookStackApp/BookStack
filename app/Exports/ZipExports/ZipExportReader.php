<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exceptions\ZipExportException;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Util\WebSafeMimeSniffer;
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
     * @return false|resource
     */
    public function streamFile(string $fileName)
    {
        return $this->zip->getStream("files/{$fileName}");
    }

    /**
     * Sniff the mime type from the file of given name.
     */
    public function sniffFileMime(string $fileName): string
    {
        $stream = $this->streamFile($fileName);
        $sniffContent = fread($stream, 2000);

        return (new WebSafeMimeSniffer())->sniff($sniffContent);
    }

    /**
     * @throws ZipExportException
     */
    public function decodeDataToExportModel(): ZipExportBook|ZipExportChapter|ZipExportPage
    {
        $data = $this->readData();
        if (isset($data['book'])) {
            return ZipExportBook::fromArray($data['book']);
        } else if (isset($data['chapter'])) {
            return ZipExportChapter::fromArray($data['chapter']);
        } else if (isset($data['page'])) {
            return ZipExportPage::fromArray($data['page']);
        }

        throw new ZipExportException("Could not identify content in ZIP file data.");
    }
}
