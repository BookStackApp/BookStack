<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Exceptions\ZipExportException;
use BookStack\Exceptions\ZipImportException;
use BookStack\Exports\Import;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Uploads\FileStorage;

class ZipImportRunner
{
    public function __construct(
        protected FileStorage $storage,
    ) {
    }

    /**
     * @throws ZipImportException
     */
    public function run(Import $import, ?Entity $parent = null): void
    {
        $zipPath = $this->getZipPath($import);
        $reader = new ZipExportReader($zipPath);

        $errors = (new ZipExportValidator($reader))->validate();
        if ($errors) {
            throw new ZipImportException(["ZIP failed to validate"]);
        }

        try {
            $exportModel = $reader->decodeDataToExportModel();
        } catch (ZipExportException $e) {
            throw new ZipImportException([$e->getMessage()]);
        }

        // Validate parent type
        if ($exportModel instanceof ZipExportBook && ($parent !== null)) {
            throw new ZipImportException(["Must not have a parent set for a Book import"]);
        } else if ($exportModel instanceof ZipExportChapter && (!$parent instanceof Book)) {
            throw new ZipImportException(["Parent book required for chapter import"]);
        } else if ($exportModel instanceof ZipExportPage && !($parent instanceof Book || $parent instanceof Chapter)) {
            throw new ZipImportException(["Parent book or chapter required for page import"]);
        }

        $this->ensurePermissionsPermitImport($exportModel);

        // TODO - Run import
    }

    /**
     * @throws ZipImportException
     */
    protected function ensurePermissionsPermitImport(ZipExportPage|ZipExportChapter|ZipExportBook $exportModel, Book|Chapter|null $parent = null): void
    {
        $errors = [];

        // TODO - Extract messages to language files
        // TODO - Ensure these are shown to users on failure

        $chapters = [];
        $pages = [];
        $images = [];
        $attachments = [];

        if ($exportModel instanceof ZipExportBook) {
            if (!userCan('book-create-all')) {
                $errors[] = 'You are lacking the required permission to create books.';
            }
            array_push($pages, ...$exportModel->pages);
            array_push($chapters, ...$exportModel->chapters);
        } else if ($exportModel instanceof ZipExportChapter) {
            $chapters[] = $exportModel;
        } else if ($exportModel instanceof ZipExportPage) {
            $pages[] = $exportModel;
        }

        foreach ($chapters as $chapter) {
            array_push($pages, ...$chapter->pages);
        }

        if (count($chapters) > 0) {
            $permission = 'chapter-create' . ($parent ? '' : '-all');
            if (!userCan($permission, $parent)) {
                $errors[] = 'You are lacking the required permission to create chapters.';
            }
        }

        foreach ($pages as $page) {
            array_push($attachments, ...$page->attachments);
            array_push($images, ...$page->images);
        }

        if (count($pages) > 0) {
            if ($parent) {
                if (!userCan('page-create', $parent)) {
                    $errors[] = 'You are lacking the required permission to create pages.';
                }
            } else {
                $hasPermission = userCan('page-create-all') || userCan('page-create-own');
                if (!$hasPermission) {
                    $errors[] = 'You are lacking the required permission to create pages.';
                }
            }
        }

        if (count($images) > 0) {
            if (!userCan('image-create-all')) {
                $errors[] = 'You are lacking the required permissions to create images.';
            }
        }

        if (count($attachments) > 0) {
            if (userCan('attachment-create-all')) {
                $errors[] = 'You are lacking the required permissions to create attachments.';
            }
        }

        if (count($errors)) {
            throw new ZipImportException($errors);
        }
    }

    protected function getZipPath(Import $import): string
    {
        if (!$this->storage->isRemote()) {
            return $this->storage->getSystemPath($import->path);
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'bszip-import-');
        $tempFile = fopen($tempFilePath, 'wb');
        $stream = $this->storage->getReadStream($import->path);
        stream_copy_to_stream($stream, $tempFile);
        fclose($tempFile);

        return $tempFilePath;
    }
}
