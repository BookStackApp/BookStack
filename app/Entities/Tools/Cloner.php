<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;

class Cloner
{

    /**
     * @var PageRepo
     */
    protected $pageRepo;

    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Clone the given page into the given parent using the provided name.
     */
    public function clonePage(Page $original, Entity $parent, string $newName): Page
    {
        $copyPage = $this->pageRepo->getNewDraftPage($parent);
        $pageData = $original->getAttributes();

        // Update name
        $pageData['name'] = $newName;

        // Copy tags from previous page if set
        if ($original->tags) {
            $pageData['tags'] = [];
            foreach ($original->tags as $tag) {
                $pageData['tags'][] = ['name' => $tag->name, 'value' => $tag->value];
            }
        }

        return $this->pageRepo->publishDraft($copyPage, $pageData);
    }

}