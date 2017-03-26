<?php namespace BookStack\Services;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\Page;
use BookStack\SearchTerm;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\JoinClause;

class SearchService
{

    protected $searchTerm;
    protected $book;
    protected $chapter;
    protected $page;
    protected $db;
    protected $permissionService;
    protected $entities;

    /**
     * SearchService constructor.
     * @param SearchTerm $searchTerm
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param Connection $db
     * @param PermissionService $permissionService
     */
    public function __construct(SearchTerm $searchTerm, Book $book, Chapter $chapter, Page $page, Connection $db, PermissionService $permissionService)
    {
        $this->searchTerm = $searchTerm;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        $this->db = $db;
        $this->entities = [
            'page' => $this->page,
            'chapter' => $this->chapter,
            'book' => $this->book
        ];
        $this->permissionService = $permissionService;
    }

    public function searchEntities($searchString, $entityType = 'all', $page = 0, $count = 20)
    {
        // TODO - Add Tag Searches
        // TODO - Add advanced custom column searches
        // TODO - Add exact match searches ("")
        // TODO - Check drafts don't show up in results
        // TODO - Move search all page to just /search?term=cat

       if ($entityType !== 'all') return $this->searchEntityTable($searchString, $entityType, $page, $count);

       $bookSearch = $this->searchEntityTable($searchString, 'book', $page, $count);
       $chapterSearch = $this->searchEntityTable($searchString, 'chapter', $page, $count);
       $pageSearch = $this->searchEntityTable($searchString, 'page', $page, $count);
       return collect($bookSearch)->merge($chapterSearch)->merge($pageSearch)->sortByDesc('score');
    }

    public function searchEntityTable($searchString, $entityType = 'page', $page = 0, $count = 20)
    {
        $termArray = explode(' ', $searchString);

        $subQuery = $this->db->table('search_terms')->select('entity_id', 'entity_type', \DB::raw('SUM(score) as score'));
        $subQuery->where(function($query) use ($termArray) {
            foreach ($termArray as $inputTerm) {
                $query->orWhere('term', 'like', $inputTerm .'%');
            }
        });

        $entity = $this->getEntity($entityType);
        $subQuery = $subQuery->groupBy('entity_type', 'entity_id');
        $entitySelect = $entity->newQuery()->join(\DB::raw('(' . $subQuery->toSql() . ') as s'), function(JoinClause $join) {
            $join->on('id', '=', 'entity_id');
        })->selectRaw($entity->getTable().'.*, s.score')->orderBy('score', 'desc')->skip($page * $count)->take($count);
        $entitySelect->mergeBindings($subQuery);
        $query = $this->permissionService->enforceEntityRestrictions($entityType, $entitySelect, 'view');
        return $query->get();
    }

    /**
     * Get an entity instance via type.
     * @param $type
     * @return Entity
     */
    protected function getEntity($type)
    {
        return $this->entities[strtolower($type)];
    }

    /**
     * Index the given entity.
     * @param Entity $entity
     */
    public function indexEntity(Entity $entity)
    {
        $this->deleteEntityTerms($entity);
        $nameTerms = $this->generateTermArrayFromText($entity->name, 5);
        $bodyTerms = $this->generateTermArrayFromText($entity->getText(), 1);
        $terms = array_merge($nameTerms, $bodyTerms);
        foreach ($terms as $index => $term) {
            $terms[$index]['entity_type'] = $entity->getMorphClass();
            $terms[$index]['entity_id'] = $entity->id;
        }
        $this->searchTerm->newQuery()->insert($terms);
    }

    /**
     * Index multiple Entities at once
     * @param Entity[] $entities
     */
    protected function indexEntities($entities) {
        $terms = [];
        foreach ($entities as $entity) {
            $nameTerms = $this->generateTermArrayFromText($entity->name, 5);
            $bodyTerms = $this->generateTermArrayFromText($entity->getText(), 1);
            foreach (array_merge($nameTerms, $bodyTerms) as $term) {
                $term['entity_id'] = $entity->id;
                $term['entity_type'] = $entity->getMorphClass();
                $terms[] = $term;
            }
        }

        $chunkedTerms = array_chunk($terms, 500);
        foreach ($chunkedTerms as $termChunk) {
            $this->searchTerm->newQuery()->insert($termChunk);
        }
    }

    /**
     * Delete and re-index the terms for all entities in the system.
     */
    public function indexAllEntities()
    {
        $this->searchTerm->truncate();

        // Chunk through all books
        $this->book->chunk(1000, function ($books) {
            $this->indexEntities($books);
        });

        // Chunk through all chapters
        $this->chapter->chunk(1000, function ($chapters) {
            $this->indexEntities($chapters);
        });

        // Chunk through all pages
        $this->page->chunk(1000, function ($pages) {
            $this->indexEntities($pages);
        });
    }

    /**
     * Delete related Entity search terms.
     * @param Entity $entity
     */
    public function deleteEntityTerms(Entity $entity)
    {
        $entity->searchTerms()->delete();
    }

    /**
     * Create a scored term array from the given text.
     * @param $text
     * @param float|int $scoreAdjustment
     * @return array
     */
    protected function generateTermArrayFromText($text, $scoreAdjustment = 1)
    {
        $tokenMap = []; // {TextToken => OccurrenceCount}
        $splitText = explode(' ', $text);
        foreach ($splitText as $token) {
            if ($token === '') continue;
            if (!isset($tokenMap[$token])) $tokenMap[$token] = 0;
            $tokenMap[$token]++;
        }

        $terms = [];
        foreach ($tokenMap as $token => $count) {
            $terms[] = [
                'term' => $token,
                'score' => $count * $scoreAdjustment
            ];
        }
        return $terms;
    }

}