<?php

namespace BookStack\Entities\Tools;

use BookStack\Actions\Tag;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Uploads\Image;
use BookStack\Uploads\ImageService;
use Illuminate\Http\UploadedFile;

class Cloner
{
    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var ChapterRepo
     */
    protected $chapterRepo;

    /**
     * @var BookRepo
     */
    protected $bookRepo;

    /**
     * @var ImageService
     */
    protected $imageService;

    public function __construct(PageRepo $pageRepo, ChapterRepo $chapterRepo, BookRepo $bookRepo, ImageService $imageService)
    {
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
        $this->bookRepo = $bookRepo;
        $this->imageService = $imageService;
    }

    /**
     * Clone the given page into the given parent using the provided name.
     */
    public function clonePage(Page $original, Entity $parent, string $newName): Page
    {
        $copyPage = $this->pageRepo->getNewDraftPage($parent);
        $pageData = $original->getAttributes();

        // Update name & tags
        $pageData['name'] = $newName;
        $pageData['tags'] = $this->entityTagsToInputArray($original);

        return $this->pageRepo->publishDraft($copyPage, $pageData);
    }

    /**
     * Clone the given page into the given parent using the provided name.
     * Clones all child pages.
     */
    public function cloneChapter(Chapter $original, Book $parent, string $newName): Chapter
    {
        $chapterDetails = $original->getAttributes();
        $chapterDetails['name'] = $newName;
        $chapterDetails['tags'] = $this->entityTagsToInputArray($original);

        $copyChapter = $this->chapterRepo->create($chapterDetails, $parent);

        if (userCan('page-create', $copyChapter)) {
            /** @var Page $page */
            foreach ($original->getVisiblePages() as $page) {
                $this->clonePage($page, $copyChapter, $page->name);
            }
        }

        return $copyChapter;
    }

    /**
     * Clone the given book.
     * Clones all child chapters & pages.
     */
    public function cloneBook(Book $original, string $newName): Book
    {
        $bookDetails = $original->getAttributes();
        $bookDetails['name'] = $newName;
        $bookDetails['tags'] = $this->entityTagsToInputArray($original);

        $copyBook = $this->bookRepo->create($bookDetails);

        $directChildren = $original->getDirectChildren();
        foreach ($directChildren as $child) {
            if ($child instanceof Chapter && userCan('chapter-create', $copyBook)) {
                $this->cloneChapter($child, $copyBook, $child->name);
            }

            if ($child instanceof Page && !$child->draft && userCan('page-create', $copyBook)) {
                $this->clonePage($child, $copyBook, $child->name);
            }
        }

        if ($original->cover) {
            try {
                $tmpImgFile = tmpfile();
                $uploadedFile = $this->imageToUploadedFile($original->cover, $tmpImgFile);
                $this->bookRepo->updateCoverImage($copyBook, $uploadedFile, false);
            } catch (\Exception $exception) {
            }
        }

        return $copyBook;
    }

    /**
     * Convert an image instance to an UploadedFile instance to mimic
     * a file being uploaded.
     */
    protected function imageToUploadedFile(Image $image, &$tmpFile): ?UploadedFile
    {
        $imgData = $this->imageService->getImageData($image);
        $tmpImgFilePath = stream_get_meta_data($tmpFile)['uri'];
        file_put_contents($tmpImgFilePath, $imgData);

        return new UploadedFile($tmpImgFilePath, basename($image->path));
    }

    /**
     * Convert the tags on the given entity to the raw format
     * that's used for incoming request data.
     */
    protected function entityTagsToInputArray(Entity $entity): array
    {
        $tags = [];

        /** @var Tag $tag */
        foreach ($entity->tags as $tag) {
            $tags[] = ['name' => $tag->name, 'value' => $tag->value];
        }

        return $tags;
    }
}
