<?php

namespace BookStack\Exports\ZipExports;

use BookStack\App\Model;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\Models\ZipExportAttachment;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportImage;
use BookStack\Exports\ZipExports\Models\ZipExportModel;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\Image;

class ZipExportReferences
{
    /** @var ZipExportPage[] */
    protected array $pages = [];
    /** @var ZipExportChapter[] */
    protected array $chapters = [];
    /** @var ZipExportBook[] */
    protected array $books = [];

    /** @var ZipExportAttachment[] */
    protected array $attachments = [];

    /** @var ZipExportImage[] */
    protected array $images = [];

    public function __construct(
        protected ZipReferenceParser $parser,
    ) {
    }

    public function addPage(ZipExportPage $page): void
    {
        if ($page->id) {
            $this->pages[$page->id] = $page;
        }

        foreach ($page->attachments as $attachment) {
            if ($attachment->id) {
                $this->attachments[$attachment->id] = $attachment;
            }
        }
    }

    public function addChapter(ZipExportChapter $chapter): void
    {
        if ($chapter->id) {
            $this->chapters[$chapter->id] = $chapter;
        }

        foreach ($chapter->pages as $page) {
            $this->addPage($page);
        }
    }

    public function addBook(ZipExportBook $book): void
    {
        if ($book->id) {
            $this->chapters[$book->id] = $book;
        }

        foreach ($book->pages as $page) {
            $this->addPage($page);
        }

        foreach ($book->chapters as $chapter) {
            $this->addChapter($chapter);
        }
    }

    public function buildReferences(ZipExportFiles $files): void
    {
        $createHandler = function (ZipExportModel $zipModel) use ($files) {
            return function (Model $model) use ($files, $zipModel) {
                return $this->handleModelReference($model, $zipModel, $files);
            };
        };

        // Parse page content first
        foreach ($this->pages as $page) {
            $handler = $createHandler($page);
            $page->html = $this->parser->parse($page->html ?? '', $handler);
            if ($page->markdown) {
                $page->markdown = $this->parser->parse($page->markdown, $handler);
            }
        }

        // Parse chapter description HTML
        foreach ($this->chapters as $chapter) {
            if ($chapter->description_html) {
                $handler = $createHandler($chapter);
                $chapter->description_html = $this->parser->parse($chapter->description_html, $handler);
            }
        }

        // Parse book description HTML
        foreach ($this->books as $book) {
            if ($book->description_html) {
                $handler = $createHandler($book);
                $book->description_html = $this->parser->parse($book->description_html, $handler);
            }
        }
    }

    protected function handleModelReference(Model $model, ZipExportModel $exportModel, ZipExportFiles $files): ?string
    {
        // Handle attachment references
        // No permission check needed here since they would only already exist in this
        // reference context if already allowed via their entity access.
        if ($model instanceof Attachment) {
            if (isset($this->attachments[$model->id])) {
                return "[[bsexport:attachment:{$model->id}]]";
            }
            return null;
        }

        // Handle image references
        if ($model instanceof Image) {
            // Only handle gallery and drawio images
            if ($model->type !== 'gallery' && $model->type !== 'drawio') {
                return null;
            }

            // We don't expect images to be part of book/chapter content
            if (!($exportModel instanceof ZipExportPage)) {
                return null;
            }

            $page = $model->getPage();
            if ($page && userCan('view', $page)) {
                if (!isset($this->images[$model->id])) {
                    $exportImage = ZipExportImage::fromModel($model, $files);
                    $this->images[$model->id] = $exportImage;
                    $exportModel->images[] = $exportImage;
                }
                return "[[bsexport:image:{$model->id}]]";
            }
            return null;
        }

        // Handle entity references
        if ($model instanceof Book && isset($this->books[$model->id])) {
            return "[[bsexport:book:{$model->id}]]";
        } else if ($model instanceof Chapter && isset($this->chapters[$model->id])) {
            return "[[bsexport:chapter:{$model->id}]]";
        } else if ($model instanceof Page && isset($this->pages[$model->id])) {
            return "[[bsexport:page:{$model->id}]]";
        }

        return null;
    }
}
