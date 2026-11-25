<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Exceptions\ZipExportException;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;

class ZipExportValidator
{
    public function __construct(
        protected ZipExportReader $reader,
    ) {
    }

    public function validate(): array
    {
        try {
            $importData = $this->reader->readData();
        } catch (ZipExportException $exception) {
            return ['format' => $exception->getMessage()];
        }

        $helper = new ZipValidationHelper($this->reader);

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
            return ['format' => trans('errors.import_zip_no_data')];
        }

        $errors = $this->flattenModelErrors($modelErrors, $keyPrefix);
        $fileErrors = $this->validateFileUse($helper);

        return array_merge($errors, $fileErrors);
    }

    /**
     * Validate the use of in-ZIP files at a global view.
     * (Per-file validation will already be done at a per-model level).
     * This validates the total size of all files, considering all uses within the ZIP.
     * Note: This only validates the declared post-extract file sizes. Actual file sizes could be different,
     * and should be validated against the declared sizes during extraction.
     */
    protected function validateFileUse(ZipValidationHelper $helper): array
    {
        $fileUses = $helper->getFileReferenceUses();
        $totalSize = 0;
        foreach ($fileUses as $file) {
            $size = max(0, $this->reader->fileSize($file));
            $totalSize += $size;
        }

        // Allow up to 5x the single item app upload limit
        // Since the ZIP itself will be validated at 1x, and an import is expected to be a creation of multiple
        // items, so this leaves a little room post de-compression.
        $maxAllowedMB = max(intval(config()->get('app.upload_limit')), 1) * 5;
        $maxAllowedBytes = $maxAllowedMB * 1000000;
        if ($totalSize > $maxAllowedBytes) {
            return ['files' => trans('validation.zip_files_total_size', ['size' => $maxAllowedMB])];
        }

        return [];
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
