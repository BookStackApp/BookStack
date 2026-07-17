<?php

namespace BookStack\Exports\ZipExports;

use BookStack\App\Model;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BaseRepo;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageResizer;

class ZipImportReferences
{
    /** @var Page[] */
    protected array $pages = [];
    /** @var Chapter[] */
    protected array $chapters = [];
    /** @var Book[] */
    protected array $books = [];
    /** @var Attachment[] */
    protected array $attachments = [];
    /** @var Image[] */
    protected array $images = [];

    /**
     * Mapping keyed by "type:old-reference-id" with values being the new imported equivalent model.
     * @var array<string, Model>
     */
    protected array $referenceMap = [];

    /** @var array<int, ZipExportPage> */
    protected array $zipExportPageMap = [];
    /** @var array<int, ZipExportChapter> */
    protected array $zipExportChapterMap = [];
    /** @var array<int, ZipExportBook> */
    protected array $zipExportBookMap = [];

    public function __construct(
        protected ZipReferenceParser $parser,
        protected BaseRepo $baseRepo,
        protected PageRepo $pageRepo,
        protected ImageResizer $imageResizer,
    ) {
    }

    protected function addReference(string $type, Model $model, ?int $importId): void
    {
        if ($importId) {
            $key = $type . ':' . $importId;
            $this->referenceMap[$key] = $model;
        }
    }

    public function addPage(Page $page, ZipExportPage $exportPage): void
    {
        $this->pages[] = $page;
        $this->zipExportPageMap[$page->id] = $exportPage;
        $this->addReference('page', $page, $exportPage->id);
    }

    public function addChapter(Chapter $chapter, ZipExportChapter $exportChapter): void
    {
        $this->chapters[] = $chapter;
        $this->zipExportChapterMap[$chapter->id] = $exportChapter;
        $this->addReference('chapter', $chapter, $exportChapter->id);
    }

    public function addBook(Book $book, ZipExportBook $exportBook): void
    {
        $this->books[] = $book;
        $this->zipExportBookMap[$book->id] = $exportBook;
        $this->addReference('book', $book, $exportBook->id);
    }

    public function addAttachment(Attachment $attachment, ?int $importId): void
    {
        $this->attachments[] = $attachment;
        $this->addReference('attachment', $attachment, $importId);
    }

    public function addImage(Image $image, ?int $importId): void
    {
        $this->images[] = $image;
        $this->addReference('image', $image, $importId);
    }

    protected function handleReference(string $type, int $id): ?string
    {
        $key = $type . ':' . $id;
        $model = $this->referenceMap[$key] ?? null;
        if ($model instanceof Entity) {
            return $model->getUrl();
        } else if ($model instanceof Image) {
            if ($model->type === 'gallery') {
                $this->imageResizer->loadGalleryThumbnailsForImage($model, false);
                return $model->thumbs['display'] ?? $model->url;
            }

            return $model->url;
        } else if ($model instanceof Attachment) {
            return $model->getUrl(false);
        }

        return null;
    }

    protected function replaceDrawingIdReferences(string $content): string
    {
        $referenceRegex = '/\sdrawio-diagram=[\'"](\d+)[\'"]/';

        $result = preg_replace_callback($referenceRegex, function ($matches) {
            $key = 'image:' . $matches[1];
            $model = $this->referenceMap[$key] ?? null;
            if ($model instanceof Image && $model->type === 'drawio') {
                return ' drawio-diagram="' . $model->id . '"';
            }
            return $matches[0];
        }, $content);

        return $result ?: $content;
    }

    public function replaceReferences(): void
    {
        foreach ($this->books as $book) {
            $exportBook = $this->zipExportBookMap[$book->id];
            $content = $exportBook->description_html ?? '';
            $parsed = $this->parser->parseReferences($content, $this->handleReference(...));

            $this->baseRepo->update($book, [
                'description_html' => $parsed,
            ]);
        }

        foreach ($this->chapters as $chapter) {
            $exportChapter = $this->zipExportChapterMap[$chapter->id];
            $content = $exportChapter->description_html ?? '';
            $parsed = $this->parser->parseReferences($content, $this->handleReference(...));

            $this->baseRepo->update($chapter, [
                'description_html' => $parsed,
            ]);
        }

        foreach ($this->pages as $page) {
            $exportPage = $this->zipExportPageMap[$page->id];
            $contentType = $exportPage->markdown ? 'markdown' : 'html';
            $content = $exportPage->markdown ?: ($exportPage->html ?: '');

            $parsed = $this->parser->parseReferences($content, $this->handleReference(...));
            $parsed = $this->replaceDrawingIdReferences($parsed);

            $this->pageRepo->setContentFromInput($page, [
                $contentType => $parsed,
            ]);
        }
    }


    /**
     * @return Image[]
     */
    public function images(): array
    {
        return $this->images;
    }

    /**
     * @return Attachment[]
     */
    public function attachments(): array
    {
        return $this->attachments;
    }
}
