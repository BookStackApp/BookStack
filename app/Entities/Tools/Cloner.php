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
use Illuminate\Support\Str;
use Throwable;

class Cloner
{
    protected PageRepo $pageRepo;
    protected ChapterRepo $chapterRepo;
    protected BookRepo $bookRepo;
    protected ImageService $imageService;

    public function __construct(PageRepo $pageRepo, ChapterRepo $chapterRepo, BookRepo $bookRepo, ImageService $imageService)
    {
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
        $this->bookRepo = $bookRepo;
        $this->imageService = $imageService;
    }

    /**
     * Clone the given page into the given parent using the provided name.
     * $oldParent should be provided if $updateLinks is set to true
     */
    public function clonePage(Page $original, Entity $parent, string $newName, bool $updateLinks, Entity $oldParent = null): Page
    {
        $copyPage = $this->pageRepo->getNewDraftPage($parent);
        $pageData = $this->entityToInputData($original);

        if ($updateLinks) {
            $this->updateLinks($pageData['html'], $oldParent->getUrl(), $parent->getUrl());
        }

        // Update name & tags
        $pageData['name'] = $newName;

        return $this->pageRepo->publishDraft($copyPage, $pageData);
    }

    /**
     * Clone the given page into the given parent using the provided name.
     * Clones all child pages.
     */
    public function cloneChapter(Chapter $original, Book $parent, string $newName): Chapter
    {
        $chapterDetails = $this->entityToInputData($original);
        $chapterDetails['name'] = $newName;

        $copyChapter = $this->chapterRepo->create($chapterDetails, $parent);

        if (userCan('page-create', $copyChapter)) {
            /** @var Page $page */
            foreach ($original->getVisiblePages() as $page) {
                // we should inform cloneChapter here if this page has links that needs to be updated
                // If pages are not visible to the user we should not update the links since the target page won't be cloned
                $this->clonePage($page, $copyChapter, $page->name, true, $original);
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
        $bookDetails = $this->entityToInputData($original);
        $bookDetails['name'] = $newName;

        $copyBook = $this->bookRepo->create($bookDetails);

        $directChildren = $original->getDirectChildren();
        foreach ($directChildren as $child) {
            if ($child instanceof Chapter && userCan('chapter-create', $copyBook)) {
                $this->cloneChapter($child, $copyBook, $child->name);
            }

            if ($child instanceof Page && !$child->draft && userCan('page-create', $copyBook)) {
                $this->clonePage($child, $copyBook, $child->name, true, $original);
            }
        }

        return $copyBook;
    }

    /**
     * Convert an entity to a raw data array of input data.
     *
     * @return array<string, mixed>
     */
    public function entityToInputData(Entity $entity): array
    {
        $inputData = $entity->getAttributes();
        $inputData['tags'] = $this->entityTagsToInputArray($entity);

        // Add a cover to the data if existing on the original entity
        if ($entity->cover instanceof Image) {
            $uploadedFile = $this->imageToUploadedFile($entity->cover);
            $inputData['image'] = $uploadedFile;
        }

        return $inputData;
    }

    /**
     * Copy the permission settings from the source entity to the target entity.
     */
    public function copyEntityPermissions(Entity $sourceEntity, Entity $targetEntity): void
    {
        $targetEntity->restricted = $sourceEntity->restricted;
        $permissions = $sourceEntity->permissions()->get(['role_id', 'action'])->toArray();
        $targetEntity->permissions()->delete();
        $targetEntity->permissions()->createMany($permissions);
        $targetEntity->rebuildPermissions();
    }

    /**
     * Convert an image instance to an UploadedFile instance to mimic
     * a file being uploaded.
     */
    protected function imageToUploadedFile(Image $image): ?UploadedFile
    {
        $imgData = $this->imageService->getImageData($image);
        $tmpImgFilePath = tempnam(sys_get_temp_dir(), 'bs_cover_clone_');
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

    private function updateLinks(string &$html, string $parentLink, string $newParentLink)
    {
        // Do a quick check to see if we have some candidates that needs to be updated
        // todo handle single quote
        if (!Str::contains($html, "href=\"$parentLink")) {
            return false;
        }

        $html = Str::replace("href=\"$parentLink\"", "href=\"$newParentLink\"", $html);

        // At this point we have candidates to update
        // Find all the slugs that needs to be updated?
        // if this is a simple link to update, e.g. link to parent only we will update the html here
        // if this is more "complex" links, that is links to other pages, we don't have the informations here
        // about the new links (because we need the new page slugs). So we'll just return the slugs that needs to be
        // updated
        //! We should take care to not consider pages that are not visible 



        // todo search for relevant links in the html attribute content and replace with newer links
        // search links using $oldParent and replace using $parent
        // if it has chapter in the url get rid of it

        $pattern = "href=\"$parentLink/page/([^\"]+)";
        
        $replacement = 'href=' . $newParentLink .' /page/${1}';

        // preg_match_all("#$pattern#", $html, $matches);
        try  {
            $html = preg_replace("#$pattern#", $replacement, $html);
        } catch (Throwable $e) {
            print($e);
        }

        return true;
    }
}
