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
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Exports\ZipExports\Models\ZipExportTag;
use BookStack\Uploads\FileStorage;
use BookStack\Uploads\ImageService;
use Illuminate\Http\UploadedFile;

class ZipImportRunner
{
    protected array $tempFilesToCleanup = []; // TODO
    protected array $createdImages = []; // TODO
    protected array $createdAttachments = []; // TODO

    public function __construct(
        protected FileStorage $storage,
        protected PageRepo $pageRepo,
        protected ChapterRepo $chapterRepo,
        protected BookRepo $bookRepo,
        protected ImageService $imageService,
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
          // TODO - In transaction?
            // TODO - Revert uploaded files if goes wrong
    }

    protected function importBook(ZipExportBook $exportBook, ZipExportReader $reader): Book
    {
        $book = $this->bookRepo->create([
            'name' => $exportBook->name,
            'description_html' => $exportBook->description_html ?? '',
            'image' => $exportBook->cover ? $this->zipFileToUploadedFile($exportBook->cover, $reader) : null,
            'tags' => $this->exportTagsToInputArray($exportBook->tags ?? []),
        ]);

        // TODO - Parse/format description_html references

        if ($book->cover) {
            $this->createdImages[] = $book->cover;
        }

        // TODO - Pages
        foreach ($exportBook->chapters as $exportChapter) {
            $this->importChapter($exportChapter, $book);
        }
        // TODO - Sort chapters/pages by order

        return $book;
    }

    protected function importChapter(ZipExportChapter $exportChapter, Book $parent, ZipExportReader $reader): Chapter
    {
        $chapter = $this->chapterRepo->create([
            'name' => $exportChapter->name,
            'description_html' => $exportChapter->description_html ?? '',
            'tags' => $this->exportTagsToInputArray($exportChapter->tags ?? []),
        ], $parent);

        // TODO - Parse/format description_html references

        $exportPages = $exportChapter->pages;
        usort($exportPages, function (ZipExportPage $a, ZipExportPage $b) {
            return ($a->priority ?? 0) - ($b->priority ?? 0);
        });

        foreach ($exportPages as $exportPage) {
            //
        }
        // TODO - Pages

        return $chapter;
    }

    protected function importPage(ZipExportPage $exportPage, Book|Chapter $parent, ZipExportReader $reader): Page
    {
        $page = $this->pageRepo->getNewDraftPage($parent);

        // TODO - Import attachments
        // TODO - Import images
        // TODO - Parse/format HTML

        $this->pageRepo->publishDraft($page, [
            'name' => $exportPage->name,
            'markdown' => $exportPage->markdown,
            'html' => $exportPage->html,
            'tags' => $this->exportTagsToInputArray($exportPage->tags ?? []),
        ]);

        return $page;
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
