<?php

namespace BookStack\Exports;

use BookStack\Exceptions\FileUploadException;
use BookStack\Exceptions\ZipExportException;
use BookStack\Exceptions\ZipValidationException;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Exports\ZipExports\ZipExportReader;
use BookStack\Exports\ZipExports\ZipExportValidator;
use BookStack\Uploads\FileStorage;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportRepo
{
    public function __construct(
        protected FileStorage $storage,
    ) {
    }

    /**
     * @return Collection<Import>
     */
    public function getVisibleImports(): Collection
    {
        $query = Import::query();

        if (!userCan('settings-manage')) {
            $query->where('created_by', user()->id);
        }

        return $query->get();
    }

    public function findVisible(int $id): Import
    {
        $query = Import::query();

        if (!userCan('settings-manage')) {
            $query->where('created_by', user()->id);
        }

        return $query->findOrFail($id);
    }

    /**
     * @throws FileUploadException
     * @throws ZipValidationException
     * @throws ZipExportException
     */
    public function storeFromUpload(UploadedFile $file): Import
    {
        $zipPath = $file->getRealPath();

        $errors = (new ZipExportValidator($zipPath))->validate();
        if ($errors) {
            throw new ZipValidationException($errors);
        }

        $reader = new ZipExportReader($zipPath);
        $exportModel = $reader->decodeDataToExportModel();

        $import = new Import();
        $import->type = match (get_class($exportModel)) {
            ZipExportPage::class => 'page',
            ZipExportChapter::class => 'chapter',
            ZipExportBook::class => 'book',
        };

        $import->name = $exportModel->name;
        $import->created_by = user()->id;
        $import->size = filesize($zipPath);

        $exportModel->metadataOnly();
        $import->metadata = json_encode($exportModel);

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

    public function runImport(Import $import, ?string $parent = null)
    {
        // TODO - Download import zip (if needed)
        // TODO - Validate zip file again
        // TODO - Check permissions before (create for main item, create for children, create for related items [image, attachments])
    }

    public function deleteImport(Import $import): void
    {
        $this->storage->delete($import->path);
        $import->delete();
    }
}
