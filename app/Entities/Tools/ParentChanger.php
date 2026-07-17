<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Chapter;
use BookStack\References\ReferenceUpdater;

class ParentChanger
{
    public function __construct(
        protected SlugGenerator $slugGenerator,
        protected ReferenceUpdater $referenceUpdater
    ) {
    }

    /**
     * Change the parent book of a chapter or page.
     */
    public function changeBook(BookChild $child, int $newBookId): void
    {
        $oldUrl = $child->getUrl();

        $child->book_id = $newBookId;
        $child->unsetRelation('book');
        $this->slugGenerator->regenerateForEntity($child);
        $child->save();

        if ($oldUrl !== $child->getUrl()) {
            $this->referenceUpdater->updateEntityReferences($child, $oldUrl);
        }

        // Update all child pages if a chapter
        if ($child instanceof Chapter) {
            foreach ($child->pages()->withTrashed()->get() as $page) {
                $this->changeBook($page, $newBookId);
            }
        }
    }
}
