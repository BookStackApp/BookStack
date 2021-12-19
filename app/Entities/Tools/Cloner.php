<?php

namespace BookStack\Entities\Tools;

use BookStack\Actions\Tag;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Repos\PageRepo;

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

    public function __construct(PageRepo $pageRepo, ChapterRepo $chapterRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
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