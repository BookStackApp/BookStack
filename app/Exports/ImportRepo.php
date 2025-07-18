<?php

namespace BookStack\Exports;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Exceptions\FileUploadException;
use BookStack\Exceptions\ZipExportException;
use BookStack\Exceptions\ZipImportException;
use BookStack\Exceptions\ZipValidationException;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Exports\ZipExports\ZipExportReader;
use BookStack\Exports\ZipExports\ZipExportValidator;
use BookStack\Exports\ZipExports\ZipImportRunner;
use BookStack\Facades\Activity;
use BookStack\Uploads\FileStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportRepo
{
    public function __construct(
        protected FileStorage $storage,
        protected ZipImportRunner $importer,
        protected EntityQueries $entityQueries,
    ) {
    }

    /**
     * @return Collection<Import>
     */
    public function getVisibleImports(): Collection
    {
        return $this->queryVisible()->get();
    }

    public function queryVisible(): Builder
    {
        $query = Import::query();

        if (!userCan('settings-manage')) {
            $query->where('created_by', user()->id);
        }

        return $query;
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
        $reader = new ZipExportReader($zipPath);

        $errors = (new ZipExportValidator($reader))->validate();
        if ($errors) {
            throw new ZipValidationException($errors);
        }

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

        Activity::add(ActivityType::IMPORT_CREATE, $import);

        return $import;
    }

    /**
     * @throws ZipImportException
     */
    public function runImport(Import $import, ?string $parent = null): Entity
    {
        $parentModel = null;
        if ($import->type === 'page' || $import->type === 'chapter') {
            $parentModel = $parent ? $this->entityQueries->findVisibleByStringIdentifier($parent) : null;
        }

        DB::beginTransaction();
        try {
            $model = $this->importer->run($import, $parentModel);
        } catch (ZipImportException $e) {
            DB::rollBack();
            $this->importer->revertStoredFiles();
            throw $e;
        }

        DB::commit();
        $this->deleteImport($import);
        Activity::add(ActivityType::IMPORT_RUN, $import);

        return $model;
    }

    public function deleteImport(Import $import): void
    {
        $this->storage->delete($import->path);
        $import->delete();

        Activity::add(ActivityType::IMPORT_DELETE, $import);
    }
}
