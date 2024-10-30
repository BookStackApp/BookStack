<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exceptions\ZipExportValidationException;
use ZipArchive;

class ZipExportValidator
{
    protected array $errors = [];

    public function __construct(
        protected string $zipPath,
    ) {
    }

    /**
     * @throws ZipExportValidationException
     */
    public function validate()
    {
        // TODO - Return type
        // TODO - extract messages to translations?

        // Validate file exists
        if (!file_exists($this->zipPath) || !is_readable($this->zipPath)) {
            $this->throwErrors("Could not read ZIP file");
        }

        // Validate file is valid zip
        $zip = new \ZipArchive();
        $opened = $zip->open($this->zipPath, ZipArchive::RDONLY);
        if ($opened !== true) {
            $this->throwErrors("Could not read ZIP file");
        }

        // Validate json data exists, including metadata
        $jsonData = $zip->getFromName('data.json') ?: '';
        $importData = json_decode($jsonData, true);
        if (!$importData) {
            $this->throwErrors("Could not decode ZIP data.json content");
        }

        if (isset($importData['book'])) {
            // TODO - Validate book
        } else if (isset($importData['chapter'])) {
            // TODO - Validate chapter
        } else if (isset($importData['page'])) {
            // TODO - Validate page
        } else {
            $this->throwErrors("ZIP file has no book, chapter or page data");
        }
    }

    /**
     * @throws ZipExportValidationException
     */
    protected function throwErrors(...$errorsToAdd): never
    {
        array_push($this->errors, ...$errorsToAdd);
        throw new ZipExportValidationException($this->errors);
    }
}
