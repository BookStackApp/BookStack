<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\EntityTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
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
     * Start a query across all entity types.
     * Combines the description/text fields into a single 'description' field.
     * @return Builder<EntityTable>
     */
    public function visibleForList(): Builder
    {
        $rawDescriptionField = DB::raw('COALESCE(description, text) as description');
        $bookSlugSelect = function (QueryBuilder $query) {
            return $query->select('slug')->from('entities as books')
                ->whereColumn('books.id', '=', 'entities.book_id')
                ->where('type', '=', 'book');
        };

        return EntityTable::query()->scopes('visible')
            ->select(['id', 'type', 'name', 'slug', 'book_id', 'chapter_id', 'created_at', 'updated_at', 'draft', 'book_slug' => $bookSlugSelect, $rawDescriptionField])
            ->leftJoin('entity_container_data', function (JoinClause $join) {
                $join->on('entity_container_data.entity_id', '=', 'entities.id')
                    ->on('entity_container_data.entity_type', '=', 'entities.type');
            })->leftJoin('entity_page_data', function (JoinClause $join) {
                $join->on('entity_page_data.page_id', '=', 'entities.id')
                    ->where('entities.type', '=', 'page');
            });
    }

    /**
     * Start a query of visible entities of the given type,
     * suitable for listing display.
     * @return Builder<Entity>
     */
    public function visibleForListForType(string $entityType): Builder
    {
        $queries = $this->getQueriesForType($entityType);
        return $queries->visibleForList();
    }

    /**
     * Start a query of visible entities of the given type,
     * suitable for using the contents of the items.
     * @return Builder<Entity>
     */
    public function visibleForContentForType(string $entityType): Builder
    {
        $queries = $this->getQueriesForType($entityType);
        return $queries->visibleForContent();
    }

    protected function getQueriesForType(string $type): ProvidesEntityQueries
    {
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
