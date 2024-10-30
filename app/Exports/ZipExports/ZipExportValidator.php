<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use ZipArchive;

class ZipExportValidator
{
    public function __construct(
        protected string $zipPath,
    ) {
    }

    public function validate(): array
    {
        // Validate file exists
        if (!file_exists($this->zipPath) || !is_readable($this->zipPath)) {
            return ['format' => "Could not read ZIP file"];
        }

        // Validate file is valid zip
        $zip = new \ZipArchive();
        $opened = $zip->open($this->zipPath, ZipArchive::RDONLY);
        if ($opened !== true) {
            return ['format' => "Could not read ZIP file"];
        }

        // Validate json data exists, including metadata
        $jsonData = $zip->getFromName('data.json') ?: '';
        $importData = json_decode($jsonData, true);
        if (!$importData) {
            return ['format' => "Could not find and decode ZIP data.json content"];
        }

        $helper = new ZipValidationHelper($zip);

        if (isset($importData['book'])) {
            $modelErrors = ZipExportBook::validate($helper, $importData['book']);
            $keyPrefix = 'book';
        } else if (isset($importData['chapter'])) {
            $modelErrors = ZipExportChapter::validate($helper, $importData['chapter']);
            $keyPrefix = 'chapter';
        } else if (isset($importData['page'])) {
            $modelErrors = ZipExportPage::validate($helper, $importData['page']);
            $keyPrefix = 'page';
        } else {
            return ['format' => "ZIP file has no book, chapter or page data"];
        }

        return $this->flattenModelErrors($modelErrors, $keyPrefix);
    }

    protected function flattenModelErrors(array $errors, string $keyPrefix): array
    {
        $flattened = [];

        foreach ($errors as $key => $error) {
            if (is_array($error)) {
                $flattened = array_merge($flattened, $this->flattenModelErrors($error, $keyPrefix . '.' . $key));
            } else {
                $flattened[$keyPrefix . '.' . $key] = $error;
            }
        }

        return $flattened;
    }
}
