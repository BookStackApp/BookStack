<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\ZipExportException;
use BookStack\Exceptions\ZipImportException;
use BookStack\Exports\Import;
use BookStack\Exports\ZipExports\Models\ZipExportAttachment;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportImage;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Exports\ZipExports\Models\ZipExportTag;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\AttachmentService;
use BookStack\Uploads\FileStorage;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageService;
use Illuminate\Http\UploadedFile;

class ZipImportRunner
{
    protected array $tempFilesToCleanup = [];

    public function __construct(
        protected FileStorage $storage,
        protected PageRepo $pageRepo,
        protected ChapterRepo $chapterRepo,
        protected BookRepo $bookRepo,
        protected ImageService $imageService,
        protected AttachmentService $attachmentService,
        protected ZipImportReferences $references,
    ) {
    }

    /**
     * Run the import.
     * Performs re-validation on zip, validation on parent provided, and permissions for importing
     * the planned content, before running the import process.
     * Returns the top-level entity item which was imported.
     * @throws ZipImportException
     */
    public function run(Import $import, ?Entity $parent = null): Entity
    {
        $zipPath = $this->getZipPath($import);
        $reader = new ZipExportReader($zipPath);

        $errors = (new ZipExportValidator($reader))->validate();
        if ($errors) {
            throw new ZipImportException([
                trans('errors.import_validation_failed'),
                ...$errors,
            ]);
        }

        try {
            $exportModel = $reader->decodeDataToExportModel();
        } catch (ZipExportException $e) {
            throw new ZipImportException([$e->getMessage()]);
        }

        // Validate parent type
        if ($exportModel instanceof ZipExportBook && ($parent !== null)) {
            throw new ZipImportException(["Must not have a parent set for a Book import."]);
        } else if ($exportModel instanceof ZipExportChapter && !($parent instanceof Book)) {
            throw new ZipImportException(["Parent book required for chapter import."]);
        } else if ($exportModel instanceof ZipExportPage && !($parent instanceof Book || $parent instanceof Chapter)) {
            throw new ZipImportException(["Parent book or chapter required for page import."]);
        }

        $this->ensurePermissionsPermitImport($exportModel, $parent);

        if ($exportModel instanceof ZipExportBook) {
            $entity = $this->importBook($exportModel, $reader);
        } else if ($exportModel instanceof ZipExportChapter) {
            $entity = $this->importChapter($exportModel, $parent, $reader);
        } else if ($exportModel instanceof ZipExportPage) {
            $entity = $this->importPage($exportModel, $parent, $reader);
        } else {
            throw new ZipImportException(['No importable data found in import data.']);
        }

        $this->references->replaceReferences();

        $reader->close();
        $this->cleanup();

        return $entity;
    }

    /**
     * Revert any files which have been stored during this import process.
     * Considers files only, and avoids the database under the
     * assumption that the database may already have been
     * reverted as part of a transaction rollback.
     */
    public function revertStoredFiles(): void
    {
        foreach ($this->references->images() as $image) {
            $this->imageService->destroyFileAtPath($image->type, $image->path);
        }

        foreach ($this->references->attachments() as $attachment) {
            if (!$attachment->external) {
                $this->attachmentService->deleteFileInStorage($attachment);
            }
        }

        $this->cleanup();
    }

    protected function cleanup(): void
    {
        foreach ($this->tempFilesToCleanup as $file) {
            unlink($file);
        }

        $this->tempFilesToCleanup = [];
    }

    protected function importBook(ZipExportBook $exportBook, ZipExportReader $reader): Book
    {
        $book = $this->bookRepo->create([
            'name' => $exportBook->name,
            'description_html' => $exportBook->description_html ?? '',
            'image' => $exportBook->cover ? $this->zipFileToUploadedFile($exportBook->cover, $reader) : null,
            'tags' => $this->exportTagsToInputArray($exportBook->tags ?? []),
        ]);

        if ($book->cover) {
            $this->references->addImage($book->cover, null);
        }

        $children = [
            ...$exportBook->chapters,
            ...$exportBook->pages,
        ];

        usort($children, function (ZipExportPage|ZipExportChapter $a, ZipExportPage|ZipExportChapter $b) {
            return ($a->priority ?? 0) - ($b->priority ?? 0);
        });

        foreach ($children as $child) {
            if ($child instanceof ZipExportChapter) {
                $this->importChapter($child, $book, $reader);
            } else if ($child instanceof ZipExportPage) {
                $this->importPage($child, $book, $reader);
            }
        }

        $this->references->addBook($book, $exportBook);

        return $book;
    }

    protected function importChapter(ZipExportChapter $exportChapter, Book $parent, ZipExportReader $reader): Chapter
    {
        $chapter = $this->chapterRepo->create([
            'name' => $exportChapter->name,
            'description_html' => $exportChapter->description_html ?? '',
            'tags' => $this->exportTagsToInputArray($exportChapter->tags ?? []),
        ], $parent);

        $exportPages = $exportChapter->pages;
        usort($exportPages, function (ZipExportPage $a, ZipExportPage $b) {
            return ($a->priority ?? 0) - ($b->priority ?? 0);
        });

        foreach ($exportPages as $exportPage) {
            $this->importPage($exportPage, $chapter, $reader);
        }

        $this->references->addChapter($chapter, $exportChapter);

        return $chapter;
    }

    protected function importPage(ZipExportPage $exportPage, Book|Chapter $parent, ZipExportReader $reader): Page
    {
        $page = $this->pageRepo->getNewDraftPage($parent);

        foreach ($exportPage->attachments as $exportAttachment) {
            $this->importAttachment($exportAttachment, $page, $reader);
        }

        foreach ($exportPage->images as $exportImage) {
            $this->importImage($exportImage, $page, $reader);
        }

        $this->pageRepo->publishDraft($page, [
            'name' => $exportPage->name,
            'markdown' => $exportPage->markdown,
            'html' => $exportPage->html,
            'tags' => $this->exportTagsToInputArray($exportPage->tags ?? []),
        ]);

        $this->references->addPage($page, $exportPage);

        return $page;
    }

    protected function importAttachment(ZipExportAttachment $exportAttachment, Page $page, ZipExportReader $reader): Attachment
    {
        if ($exportAttachment->file) {
            $file = $this->zipFileToUploadedFile($exportAttachment->file, $reader);
            $attachment = $this->attachmentService->saveNewUpload($file, $page->id);
            $attachment->name = $exportAttachment->name;
            $attachment->save();
        } else {
            $attachment = $this->attachmentService->saveNewFromLink(
                $exportAttachment->name,
                $exportAttachment->link ?? '',
                $page->id,
            );
        }

        $this->references->addAttachment($attachment, $exportAttachment->id);

        return $attachment;
    }

    protected function importImage(ZipExportImage $exportImage, Page $page, ZipExportReader $reader): Image
    {
        $mime = $reader->sniffFileMime($exportImage->file);
        $extension = explode('/', $mime)[1];

        $file = $this->zipFileToUploadedFile($exportImage->file, $reader);
        $image = $this->imageService->saveNewFromUpload(
            $file,
            $exportImage->type,
            $page->id,
            null,
            null,
            true,
            $exportImage->name . '.' . $extension,
        );

        $image->name = $exportImage->name;
        $image->save();

        $this->references->addImage($image, $exportImage->id);

        return $image;
    }

    protected function exportTagsToInputArray(array $exportTags): array
    {
        $tags = [];

        /** @var ZipExportTag $tag */
        foreach ($exportTags as $tag) {
            $tags[] = ['name' => $tag->name, 'value' => $tag->value ?? ''];
        }

        return $tags;
    }

    protected function zipFileToUploadedFile(string $fileName, ZipExportReader $reader): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'bszipextract');
        $fileStream = $reader->streamFile($fileName);
        $tempStream = fopen($tempPath, 'wb');
        stream_copy_to_stream($fileStream, $tempStream);
        fclose($tempStream);

        $this->tempFilesToCleanup[] = $tempPath;

        return new UploadedFile($tempPath, $fileName);
    }

    /**
     * @throws ZipImportException
     */
    protected function ensurePermissionsPermitImport(ZipExportPage|ZipExportChapter|ZipExportBook $exportModel, Book|Chapter|null $parent = null): void
    {
        $errors = [];

        $chapters = [];
        $pages = [];
        $images = [];
        $attachments = [];

        if ($exportModel instanceof ZipExportBook) {
            if (!userCan('book-create-all')) {
                $errors[] = trans('errors.import_perms_books');
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
                $errors[] = trans('errors.import_perms_chapters');
            }
        }

        foreach ($pages as $page) {
            array_push($attachments, ...$page->attachments);
            array_push($images, ...$page->images);
        }

        if (count($pages) > 0) {
            if ($parent) {
                if (!userCan('page-create', $parent)) {
                    $errors[] = trans('errors.import_perms_pages');
                }
            } else {
                $hasPermission = userCan('page-create-all') || userCan('page-create-own');
                if (!$hasPermission) {
                    $errors[] = trans('errors.import_perms_pages');
                }
            }
        }

        if (count($images) > 0) {
            if (!userCan('image-create-all')) {
                $errors[] = trans('errors.import_perms_images');
            }
        }

        if (count($attachments) > 0) {
            if (!userCan('attachment-create-all')) {
                $errors[] = trans('errors.import_perms_attachments');
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

        $this->tempFilesToCleanup[] = $tempFilePath;

        return $tempFilePath;
    }
}
