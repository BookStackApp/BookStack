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
