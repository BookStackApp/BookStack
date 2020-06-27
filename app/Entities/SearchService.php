<?php namespace BookStack\Entities;

use BookStack\Auth\Permissions\PermissionService;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchService
{
    /**
     * @var SearchTerm
     */
    protected $searchTerm;

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var PermissionService
     */
    protected $permissionService;


    /**
     * Acceptable operators to be used in a query
     * @var array
     */
    protected $queryOperators = ['<=', '>=', '=', '<', '>', 'like', '!='];

    /**
     * SearchService constructor.
     */
    public function __construct(SearchTerm $searchTerm, EntityProvider $entityProvider, Connection $db, PermissionService $permissionService)
    {
        $this->searchTerm = $searchTerm;
        $this->entityProvider = $entityProvider;
        $this->db = $db;
        $this->permissionService = $permissionService;
    }

    /**
     * Set the database connection
     */
    public function setConnection(Connection $connection)
    {
        $this->db = $connection;
    }

    /**
     * Search all entities in the system.
     * The provided count is for each entity to search,
     * Total returned could can be larger and not guaranteed.
     */
    public function searchEntities(SearchOptions $searchOpts, string $entityType = 'all', int $page = 1, int $count = 20, string $action = 'view'): array
    {
        $entityTypes = array_keys($this->entityProvider->all());
        $entityTypesToSearch = $entityTypes;

        if ($entityType !== 'all') {
            $entityTypesToSearch = $entityType;
        } else if (isset($searchOpts->filters['type'])) {
            $entityTypesToSearch = explode('|', $searchOpts->filters['type']);
        }

        $results = collect();
        $total = 0;
        $hasMore = false;

        foreach ($entityTypesToSearch as $entityType) {
            if (!in_array($entityType, $entityTypes)) {
                continue;
            }
            $search = $this->searchEntityTable($searchOpts, $entityType, $page, $count, $action);
            $entityTotal = $this->searchEntityTable($searchOpts, $entityType, $page, $count, $action, true);
            if ($entityTotal > $page * $count) {
                $hasMore = true;
            }
            $total += $entityTotal;
            $results = $results->merge($search);
        }

        return [
            'total' => $total,
            'count' => count($results),
            'has_more' => $hasMore,
            'results' => $results->sortByDesc('score')->values(),
        ];
    }


    /**
     * Search a book for entities
     */
    public function searchBook(int $bookId, string $searchString): Collection
    {
        $opts = SearchOptions::fromString($searchString);
        $entityTypes = ['page', 'chapter'];
        $entityTypesToSearch = isset($opts->filters['type']) ? explode('|', $opts->filters['type']) : $entityTypes;

        $results = collect();
        foreach ($entityTypesToSearch as $entityType) {
            if (!in_array($entityType, $entityTypes)) {
                continue;
            }
            $search = $this->buildEntitySearchQuery($opts, $entityType)->where('book_id', '=', $bookId)->take(20)->get();
            $results = $results->merge($search);
        }
        return $results->sortByDesc('score')->take(20);
    }

    /**
     * Search a book for entities
     */
    public function searchChapter(int $chapterId, string $searchString): Collection
    {
        $opts = SearchOptions::fromString($searchString);
        $pages = $this->buildEntitySearchQuery($opts, 'page')->where('chapter_id', '=', $chapterId)->take(20)->get();
        return $pages->sortByDesc('score');
    }

    /**
     * Search across a particular entity type.
     * Setting getCount = true will return the total
     * matching instead of the items themselves.
     * @return \Illuminate\Database\Eloquent\Collection|int|static[]
     */
    public function searchEntityTable(SearchOptions $searchOpts, string $entityType = 'page', int $page = 1, int $count = 20, string $action = 'view', bool $getCount = false)
    {
        $query = $this->buildEntitySearchQuery($searchOpts, $entityType, $action);
        if ($getCount) {
            return $query->count();
        }

        $query = $query->skip(($page-1) * $count)->take($count);
        return $query->get();
    }

    /**
     * Create a search query for an entity
     */
    protected function buildEntitySearchQuery(SearchOptions $searchOpts, string $entityType = 'page', string $action = 'view'): EloquentBuilder
    {
        $entity = $this->entityProvider->get($entityType);
        $entitySelect = $entity->newQuery();

        // Handle normal search terms
        if (count($searchOpts->searches) > 0) {
            $subQuery = $this->db->table('search_terms')->select('entity_id', 'entity_type', \DB::raw('SUM(score) as score'));
            $subQuery->where('entity_type', '=', $entity->getMorphClass());
            $subQuery->where(function (Builder $query) use ($searchOpts) {
                foreach ($searchOpts->searches as $inputTerm) {
                    $query->orWhere('term', 'like', $inputTerm .'%');
                }
            })->groupBy('entity_type', 'entity_id');
            $entitySelect->join(\DB::raw('(' . $subQuery->toSql() . ') as s'), function (JoinClause $join) {
                $join->on('id', '=', 'entity_id');
            })->selectRaw($entity->getTable().'.*, s.score')->orderBy('score', 'desc');
            $entitySelect->mergeBindings($subQuery);
        }

        // Handle exact term matching
        if (count($searchOpts->exacts) > 0) {
            $entitySelect->where(function (EloquentBuilder $query) use ($searchOpts, $entity) {
                foreach ($searchOpts->exacts as $inputTerm) {
                    $query->where(function (EloquentBuilder $query) use ($inputTerm, $entity) {
                        $query->where('name', 'like', '%'.$inputTerm .'%')
                            ->orWhere($entity->textField, 'like', '%'.$inputTerm .'%');
                    });
                }
            });
        }

        // Handle tag searches
        foreach ($searchOpts->tags as $inputTerm) {
            $this->applyTagSearch($entitySelect, $inputTerm);
        }

        // Handle filters
        foreach ($searchOpts->filters as $filterTerm => $filterValue) {
            $functionName = Str::camel('filter_' . $filterTerm);
            if (method_exists($this, $functionName)) {
                $this->$functionName($entitySelect, $entity, $filterValue);
            }
        }

        return $this->permissionService->enforceEntityRestrictions($entityType, $entitySelect, $action);
    }

    /**
     * Get the available query operators as a regex escaped list.
     */
    protected function getRegexEscapedOperators(): string
    {
        $escapedOperators = [];
        foreach ($this->queryOperators as $operator) {
            $escapedOperators[] = preg_quote($operator);
        }
        return join('|', $escapedOperators);
    }

    /**
     * Apply a tag search term onto a entity query.
     */
    protected function applyTagSearch(EloquentBuilder $query, string $tagTerm): EloquentBuilder
    {
        preg_match("/^(.*?)((".$this->getRegexEscapedOperators().")(.*?))?$/", $tagTerm, $tagSplit);
        $query->whereHas('tags', function (EloquentBuilder $query) use ($tagSplit) {
            $tagName = $tagSplit[1];
            $tagOperator = count($tagSplit) > 2 ? $tagSplit[3] : '';
            $tagValue = count($tagSplit) > 3 ? $tagSplit[4] : '';
            $validOperator = in_array($tagOperator, $this->queryOperators);
            if (!empty($tagOperator) && !empty($tagValue) && $validOperator) {
                if (!empty($tagName)) {
                    $query->where('name', '=', $tagName);
                }
                if (is_numeric($tagValue) && $tagOperator !== 'like') {
                    // We have to do a raw sql query for this since otherwise PDO will quote the value and MySQL will
                    // search the value as a string which prevents being able to do number-based operations
                    // on the tag values. We ensure it has a numeric value and then cast it just to be sure.
                    $tagValue = (float) trim($query->getConnection()->getPdo()->quote($tagValue), "'");
                    $query->whereRaw("value ${tagOperator} ${tagValue}");
                } else {
                    $query->where('value', $tagOperator, $tagValue);
                }
            } else {
                $query->where('name', '=', $tagName);
            }
        });
        return $query;
    }

    /**
     * Index the given entity.
     */
    public function indexEntity(Entity $entity)
    {
        $this->deleteEntityTerms($entity);
        $nameTerms = $this->generateTermArrayFromText($entity->name, 5 * $entity->searchFactor);
        $bodyTerms = $this->generateTermArrayFromText($entity->getText(), 1 * $entity->searchFactor);
        $terms = array_merge($nameTerms, $bodyTerms);
        foreach ($terms as $index => $term) {
            $terms[$index]['entity_type'] = $entity->getMorphClass();
            $terms[$index]['entity_id'] = $entity->id;
        }
        $this->searchTerm->newQuery()->insert($terms);
    }

    /**
     * Index multiple Entities at once
     * @param \BookStack\Entities\Entity[] $entities
     */
    protected function indexEntities($entities)
    {
        $terms = [];
        foreach ($entities as $entity) {
            $nameTerms = $this->generateTermArrayFromText($entity->name, 5 * $entity->searchFactor);
            $bodyTerms = $this->generateTermArrayFromText($entity->getText(), 1 * $entity->searchFactor);
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

        foreach ($this->entityProvider->all() as $entityModel) {
            $selectFields = ['id', 'name', $entityModel->textField];
            $entityModel->newQuery()->select($selectFields)->chunk(1000, function ($entities) {
                $this->indexEntities($entities);
            });
        }
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
        $splitChars = " \n\t.,!?:;()[]{}<>`'\"";
        $token = strtok($text, $splitChars);

        while ($token !== false) {
            if (!isset($tokenMap[$token])) {
                $tokenMap[$token] = 0;
            }
            $tokenMap[$token]++;
            $token = strtok($splitChars);
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




    /**
     * Custom entity search filters
     */

    protected function filterUpdatedAfter(EloquentBuilder $query, Entity $model, $input)
    {
        try {
            $date = date_create($input);
        } catch (\Exception $e) {
            return;
        }
        $query->where('updated_at', '>=', $date);
    }

    protected function filterUpdatedBefore(EloquentBuilder $query, Entity $model, $input)
    {
        try {
            $date = date_create($input);
        } catch (\Exception $e) {
            return;
        }
        $query->where('updated_at', '<', $date);
    }

    protected function filterCreatedAfter(EloquentBuilder $query, Entity $model, $input)
    {
        try {
            $date = date_create($input);
        } catch (\Exception $e) {
            return;
        }
        $query->where('created_at', '>=', $date);
    }

    protected function filterCreatedBefore(EloquentBuilder $query, Entity $model, $input)
    {
        try {
            $date = date_create($input);
        } catch (\Exception $e) {
            return;
        }
        $query->where('created_at', '<', $date);
    }

    protected function filterCreatedBy(EloquentBuilder $query, Entity $model, $input)
    {
        if (!is_numeric($input) && $input !== 'me') {
            return;
        }
        if ($input === 'me') {
            $input = user()->id;
        }
        $query->where('created_by', '=', $input);
    }

    protected function filterUpdatedBy(EloquentBuilder $query, Entity $model, $input)
    {
        if (!is_numeric($input) && $input !== 'me') {
            return;
        }
        if ($input === 'me') {
            $input = user()->id;
        }
        $query->where('updated_by', '=', $input);
    }

    protected function filterInName(EloquentBuilder $query, Entity $model, $input)
    {
        $query->where('name', 'like', '%' .$input. '%');
    }

    protected function filterInTitle(EloquentBuilder $query, Entity $model, $input)
    {
        $this->filterInName($query, $model, $input);
    }

    protected function filterInBody(EloquentBuilder $query, Entity $model, $input)
    {
        $query->where($model->textField, 'like', '%' .$input. '%');
    }

    protected function filterIsRestricted(EloquentBuilder $query, Entity $model, $input)
    {
        $query->where('restricted', '=', true);
    }

    protected function filterViewedByMe(EloquentBuilder $query, Entity $model, $input)
    {
        $query->whereHas('views', function ($query) {
            $query->where('user_id', '=', user()->id);
        });
    }

    protected function filterNotViewedByMe(EloquentBuilder $query, Entity $model, $input)
    {
        $query->whereDoesntHave('views', function ($query) {
            $query->where('user_id', '=', user()->id);
        });
    }

    protected function filterSortBy(EloquentBuilder $query, Entity $model, $input)
    {
        $functionName = Str::camel('sort_by_' . $input);
        if (method_exists($this, $functionName)) {
            $this->$functionName($query, $model);
        }
    }


    /**
     * Sorting filter options
     */

    protected function sortByLastCommented(EloquentBuilder $query, Entity $model)
    {
        $commentsTable = $this->db->getTablePrefix() . 'comments';
        $morphClass = str_replace('\\', '\\\\', $model->getMorphClass());
        $commentQuery = $this->db->raw('(SELECT c1.entity_id, c1.entity_type, c1.created_at as last_commented FROM '.$commentsTable.' c1 LEFT JOIN '.$commentsTable.' c2 ON (c1.entity_id = c2.entity_id AND c1.entity_type = c2.entity_type AND c1.created_at < c2.created_at) WHERE c1.entity_type = \''. $morphClass .'\' AND c2.created_at IS NULL) as comments');

        $query->join($commentQuery, $model->getTable() . '.id', '=', 'comments.entity_id')->orderBy('last_commented', 'desc');
    }
}
