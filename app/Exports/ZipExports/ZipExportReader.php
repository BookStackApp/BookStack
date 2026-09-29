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

        $info = $this->zip->statName('data.json');
        if ($info === false) {
            throw new ZipExportException(trans('errors.import_zip_cant_decode_data'));
        }

        $maxSize = max(intval(config()->get('app.upload_limit')), 1) * 1000000;
        $dataSize = $info['size'];
        if ($dataSize > $maxSize) {
            throw new ZipExportException(trans('errors.import_zip_data_too_large'));
        }

        // Validate json data exists, including metadata
        // The read data size is bound to at least 1 byte to prevent passing 0, which would read without a limit
        $jsonData = $this->zip->getFromName('data.json', max($dataSize, 1)) ?: '';
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
     * Get the DECLARED (not actual) uncompressed size of a file within the ZIP.
     * Returns -1 if the file does not exist.
     */
    public function fileSize(string $fileName): int
    {
        $fileInfo = $this->zip->statName("files/{$fileName}");
        if ($fileInfo === false) {
            return -1;
        }

        return $fileInfo['size'];
    }

    /**
     * Check that the file of given name within the ZIP is within the app file size limit.
     * WARNING: This only checks the declared size of the file, not the actual size of the file.
     * You will then need to ensure the file is read/streamed/copied out with the size as a limit.
     */
    public function fileWithinSizeLimit(string $fileName): bool
    {
        $fileSize = $this->fileSize($fileName);
        if ($fileSize < 0) {
            return false;
        }

        $maxSize = max(intval(config()->get('app.upload_limit')), 1) * 1000000;
        return $fileSize <= $maxSize;
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
