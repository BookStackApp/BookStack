<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\EntityQueries;
use Illuminate\Support\Collection;

class SiblingFetcher
{
    public function __construct(
        protected EntityQueries $queries,
    ) {
    }

    /**
     * Search among the siblings of the entity of given type and id.
     */
    public function fetch(string $entityType, int $entityId): Collection
    {
        $entity = (new EntityProvider())->get($entityType)->visible()->findOrFail($entityId);
        $entities = [];

        // Page in chapter
        if ($entity instanceof Page && $entity->chapter) {
            $entities = $entity->chapter->getVisiblePages();
        }

        // Page in book or chapter
        if (($entity instanceof Page && !$entity->chapter) || $entity instanceof Chapter) {
            $entities = $entity->book->getDirectVisibleChildren();
        }

        // Book
        // Gets just the books in a shelf if shelf is in context
        if ($entity instanceof Book) {
            $contextShelf = (new ShelfContext())->getContextualShelfForBook($entity);
            if ($contextShelf) {
                $entities = $contextShelf->visibleBooks()->get();
            } else {
                $entities = $this->queries->books->visibleForList()->get();
            }
        }

        // Shelf
        if ($entity instanceof Bookshelf) {
            $entities = $this->queries->shelves->visibleForList()->get();
        }

        return $entities;
    }
}
