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

    /**
     * SearchService constructor.
     * @param SearchTerm $searchTerm
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param Connection $db
     */
    public function __construct(SearchTerm $searchTerm, Book $book, Chapter $chapter, Page $page, Connection $db)
    {
        $this->searchTerm = $searchTerm;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        $this->db = $db;
    }

    public function searchEntities($searchString, $entityType = 'all')
    {
        // TODO - Add Tag Searches
        // TODO - Add advanced custom column searches
        // TODO - Add exact match searches ("")

        $termArray = explode(' ', $searchString);

        $subQuery = $this->db->table('search_terms')->select('entity_id', 'entity_type', \DB::raw('SUM(score) as score'));
        $subQuery->where(function($query) use ($termArray) {
            foreach ($termArray as $inputTerm) {
                $query->orWhere('term', 'like', $inputTerm .'%');
            }
        });

        $subQuery = $subQuery->groupBy('entity_type', 'entity_id');
        $pageSelect = $this->db->table('pages as e')->join(\DB::raw('(' . $subQuery->toSql() . ') as s'), function(JoinClause $join) {
            $join->on('e.id', '=', 's.entity_id');
        })->selectRaw('e.*, s.score')->orderBy('score', 'desc');
        $pageSelect->mergeBindings($subQuery);
        dd($pageSelect->toSql());
        // TODO - Continue from here
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
        $entity->searchTerms()->createMany($terms);
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
        $this->searchTerm->insert($terms);
    }

    /**
     * Delete and re-index the terms for all entities in the system.
     */
    public function indexAllEntities()
    {
        $this->searchTerm->truncate();

        // Chunk through all books
        $this->book->chunk(500, function ($books) {
            $this->indexEntities($books);
        });

        // Chunk through all chapters
        $this->chapter->chunk(500, function ($chapters) {
            $this->indexEntities($chapters);
        });

        // Chunk through all pages
        $this->page->chunk(500, function ($pages) {
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