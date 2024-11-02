<?php

namespace BookStack\Exports;

use BookStack\Exceptions\ZipValidationException;
use BookStack\Exports\ZipExports\ZipExportReader;
use BookStack\Exports\ZipExports\ZipExportValidator;
use BookStack\Uploads\FileStorage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportRepo
{
    public function __construct(
        protected FileStorage $storage,
    ) {
    }

    public function storeFromUpload(UploadedFile $file): Import
    {
        $zipPath = $file->getRealPath();

        $errors = (new ZipExportValidator($zipPath))->validate();
        if ($errors) {
            throw new ZipValidationException($errors);
        }

        $zipEntityInfo = (new ZipExportReader($zipPath))->getEntityInfo();
        $import = new Import();
        $import->name = $zipEntityInfo['name'];
        $import->book_count = $zipEntityInfo['book_count'];
        $import->chapter_count = $zipEntityInfo['chapter_count'];
        $import->page_count = $zipEntityInfo['page_count'];
        $import->created_by = user()->id;
        $import->size = filesize($zipPath);

        $path = $this->storage->uploadFile(
            $file,
            'uploads/files/imports/',
            '',
            'zip'
        );

        $import->path = $path;
        $import->save();

        return $import;
    }
}
