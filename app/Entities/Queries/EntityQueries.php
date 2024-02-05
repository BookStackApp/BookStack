<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Entity;

class EntityQueries
{
    public function __construct(
        public BookshelfQueries $shelves,
        public BookQueries $books,
        public ChapterQueries $chapters,
        public PageQueries $pages,
        public PageRevisionQueries $revisions,
    ) {
    }

    /**
     * Find an entity via an identifier string in the format:
     * {type}:{id}
     * Example: (book:5).
     */
    public function findVisibleByStringIdentifier(string $identifier): ?Entity
    {
        $explodedId = explode(':', $identifier);
        $entityType = $explodedId[0];
        $entityId = intval($explodedId[1]);

        /** @var ?ProvidesEntityQueries $queries */
        $queries = match ($entityType) {
            'page' => $this->pages,
            'chapter' => $this->chapters,
            'book' => $this->books,
            'bookshelf' => $this->shelves,
            default => null,
        };

        if (is_null($queries)) {
            return null;
        }

        return $queries->findVisibleById($entityId);
    }
}
