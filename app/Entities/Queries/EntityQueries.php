<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

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
        $queries = $this->getQueriesForType($entityType);

        return $queries->findVisibleById($entityId);
    }

    /**
     * Start a query of visible entities of the given type,
     * suitable for listing display.
     */
    public function visibleForList(string $entityType): Builder
    {
        $queries = $this->getQueriesForType($entityType);
        return $queries->visibleForList();
    }

    protected function getQueriesForType(string $type): ProvidesEntityQueries
    {
        /** @var ?ProvidesEntityQueries $queries */
        $queries = match ($type) {
            'page' => $this->pages,
            'chapter' => $this->chapters,
            'book' => $this->books,
            'bookshelf' => $this->shelves,
            default => null,
        };

        if (is_null($queries)) {
            throw new InvalidArgumentException("No entity query class configured for {$type}");
        }

        return $queries;
    }
}
