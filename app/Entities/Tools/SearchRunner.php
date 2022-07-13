<?php

namespace BookStack\Entities\Tools;

use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Auth\User;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\SearchTerm;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SplObjectStorage;

class SearchRunner
{

    protected EntityProvider $entityProvider;
    protected PermissionApplicator $permissions;

    /**
     * Acceptable operators to be used in a query.
     *
     * @var string[]
     */
    protected $queryOperators = ['<=', '>=', '=', '<', '>', 'like', '!='];

    /**
     * Retain a cache of score adjusted terms for specific search options.
     * From PHP>=8 this can be made into a WeakMap instead.
     *
     * @var SplObjectStorage
     */
    protected $termAdjustmentCache;

    public function __construct(EntityProvider $entityProvider, PermissionApplicator $permissions)
    {
        $this->entityProvider = $entityProvider;
        $this->permissions = $permissions;
        $this->termAdjustmentCache = new SplObjectStorage();
    }

    /**
     * Search all entities in the system.
     * The provided count is for each entity to search,
     * Total returned could be larger and not guaranteed.
     *
     * @return array{total: int, count: int, has_more: bool, results: Entity[]}
     */
    public function searchEntities(SearchOptions $searchOpts, string $entityType = 'all', int $page = 1, int $count = 20): array
    {
        $entityTypes = array_keys($this->entityProvider->all());
        $entityTypesToSearch = $entityTypes;

        if ($entityType !== 'all') {
            $entityTypesToSearch = $entityType;
        } elseif (isset($searchOpts->filters['type'])) {
            $entityTypesToSearch = explode('|', $searchOpts->filters['type']);
        }

        $results = collect();
        $total = 0;
        $hasMore = false;

        foreach ($entityTypesToSearch as $entityType) {
            if (!in_array($entityType, $entityTypes)) {
                continue;
            }

            $entityModelInstance = $this->entityProvider->get($entityType);
            $searchQuery = $this->buildQuery($searchOpts, $entityModelInstance);
            $entityTotal = $searchQuery->count();
            $searchResults = $this->getPageOfDataFromQuery($searchQuery, $entityModelInstance, $page, $count);

            if ($entityTotal > ($page * $count)) {
                $hasMore = true;
            }

            $total += $entityTotal;
            $results = $results->merge($searchResults);
        }

        return [
            'total'    => $total,
            'count'    => count($results),
            'has_more' => $hasMore,
            'results'  => $results->sortByDesc('score')->values(),
        ];
    }

    /**
     * Search a book for entities.
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

            $entityModelInstance = $this->entityProvider->get($entityType);
            $search = $this->buildQuery($opts, $entityModelInstance)->where('book_id', '=', $bookId)->take(20)->get();
            $results = $results->merge($search);
        }

        return $results->sortByDesc('score')->take(20);
    }

    /**
     * Search a chapter for entities.
     */
    public function searchChapter(int $chapterId, string $searchString): Collection
    {
        $opts = SearchOptions::fromString($searchString);
        $entityModelInstance = $this->entityProvider->get('page');
        $pages = $this->buildQuery($opts, $entityModelInstance)->where('chapter_id', '=', $chapterId)->take(20)->get();

        return $pages->sortByDesc('score');
    }

    /**
     * Get a page of result data from the given query based on the provided page parameters.
     */
    protected function getPageOfDataFromQuery(EloquentBuilder $query, Entity $entityModelInstance, int $page = 1, int $count = 20): EloquentCollection
    {
        $relations = ['tags'];

        if ($entityModelInstance instanceof BookChild) {
            $relations['book'] = function (BelongsTo $query) {
                $query->scopes('visible');
            };
        }

        if ($entityModelInstance instanceof Page) {
            $relations['chapter'] = function (BelongsTo $query) {
                $query->scopes('visible');
            };
        }

        return $query->clone()
            ->with(array_filter($relations))
            ->skip(($page - 1) * $count)
            ->take($count)
            ->get();
    }

    /**
     * Create a search query for an entity.
     */
    protected function buildQuery(SearchOptions $searchOpts, Entity $entityModelInstance): EloquentBuilder
    {
        $entityQuery = $entityModelInstance->newQuery();

        if ($entityModelInstance instanceof Page) {
            $entityQuery->select($entityModelInstance::$listAttributes);
        } else {
            $entityQuery->select(['*']);
        }

        // Handle normal search terms
        $this->applyTermSearch($entityQuery, $searchOpts, $entityModelInstance);

        // Handle exact term matching
        foreach ($searchOpts->exacts as $inputTerm) {
            $entityQuery->where(function (EloquentBuilder $query) use ($inputTerm, $entityModelInstance) {
                $query->where('name', 'like', '%' . $inputTerm . '%')
                    ->orWhere($entityModelInstance->textField, 'like', '%' . $inputTerm . '%');
            });
        }

        // Handle tag searches
        foreach ($searchOpts->tags as $inputTerm) {
            $this->applyTagSearch($entityQuery, $inputTerm);
        }

        // Handle filters
        foreach ($searchOpts->filters as $filterTerm => $filterValue) {
            $functionName = Str::camel('filter_' . $filterTerm);
            if (method_exists($this, $functionName)) {
                $this->$functionName($entityQuery, $entityModelInstance, $filterValue);
            }
        }

        return $this->permissions->enforceEntityRestrictions($entityModelInstance, $entityQuery);
    }

    /**
     * For the given search query, apply the queries for handling the regular search terms.
     */
    protected function applyTermSearch(EloquentBuilder $entityQuery, SearchOptions $options, Entity $entity): void
    {
        $terms = $options->searches;
        if (count($terms) === 0) {
            return;
        }

        $scoredTerms = $this->getTermAdjustments($options);
        $scoreSelect = $this->selectForScoredTerms($scoredTerms);

        $subQuery = DB::table('search_terms')->select([
            'entity_id',
            'entity_type',
            DB::raw($scoreSelect['statement']),
        ]);

        $subQuery->addBinding($scoreSelect['bindings'], 'select');

        $subQuery->where('entity_type', '=', $entity->getMorphClass());
        $subQuery->where(function (Builder $query) use ($terms) {
            foreach ($terms as $inputTerm) {
                $query->orWhere('term', 'like', $inputTerm . '%');
            }
        });
        $subQuery->groupBy('entity_type', 'entity_id');

        $entityQuery->joinSub($subQuery, 's', 'id', '=', 'entity_id');
        $entityQuery->addSelect('s.score');
        $entityQuery->orderBy('score', 'desc');
    }

    /**
     * Create a select statement, with prepared bindings, for the given
     * set of scored search terms.
     *
     * @param array<string, float> $scoredTerms
     *
     * @return array{statement: string, bindings: string[]}
     */
    protected function selectForScoredTerms(array $scoredTerms): array
    {
        // Within this we walk backwards to create the chain of 'if' statements
        // so that each previous statement is used in the 'else' condition of
        // the next (earlier) to be built. We start at '0' to have no score
        // on no match (Should never actually get to this case).
        $ifChain = '0';
        $bindings = [];
        foreach ($scoredTerms as $term => $score) {
            $ifChain = 'IF(term like ?, score * ' . (float) $score . ', ' . $ifChain . ')';
            $bindings[] = $term . '%';
        }

        return [
            'statement' => 'SUM(' . $ifChain . ') as score',
            'bindings'  => array_reverse($bindings),
        ];
    }

    /**
     * For the terms in the given search options, query their popularity across all
     * search terms then provide that back as score adjustment multiplier applicable
     * for their rarity. Returns an array of float multipliers, keyed by term.
     *
     * @return array<string, float>
     */
    protected function getTermAdjustments(SearchOptions $options): array
    {
        if (isset($this->termAdjustmentCache[$options])) {
            return $this->termAdjustmentCache[$options];
        }

        $termQuery = SearchTerm::query()->toBase();
        $whenStatements = [];
        $whenBindings = [];

        foreach ($options->searches as $term) {
            $whenStatements[] = 'WHEN term LIKE ? THEN ?';
            $whenBindings[] = $term . '%';
            $whenBindings[] = $term;

            $termQuery->orWhere('term', 'like', $term . '%');
        }

        $case = 'CASE ' . implode(' ', $whenStatements) . ' END';
        $termQuery->selectRaw($case . ' as term', $whenBindings);
        $termQuery->selectRaw('COUNT(*) as count');
        $termQuery->groupByRaw($case, $whenBindings);

        $termCounts = $termQuery->pluck('count', 'term')->toArray();
        $adjusted = $this->rawTermCountsToAdjustments($termCounts);

        $this->termAdjustmentCache[$options] = $adjusted;

        return $this->termAdjustmentCache[$options];
    }

    /**
     * Convert counts of terms into a relative-count normalised multiplier.
     *
     * @param array<string, int> $termCounts
     *
     * @return array<string, int>
     */
    protected function rawTermCountsToAdjustments(array $termCounts): array
    {
        if (empty($termCounts)) {
            return [];
        }

        $multipliers = [];
        $max = max(array_values($termCounts));

        foreach ($termCounts as $term => $count) {
            $percent = round($count / $max, 5);
            $multipliers[$term] = 1.3 - $percent;
        }

        return $multipliers;
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

        return implode('|', $escapedOperators);
    }

    /**
     * Apply a tag search term onto a entity query.
     */
    protected function applyTagSearch(EloquentBuilder $query, string $tagTerm): EloquentBuilder
    {
        preg_match('/^(.*?)((' . $this->getRegexEscapedOperators() . ')(.*?))?$/', $tagTerm, $tagSplit);
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
                    /** @var Connection $connection */
                    $connection = $query->getConnection();
                    $tagValue = (float) trim($connection->getPdo()->quote($tagValue), "'");
                    $query->whereRaw("value {$tagOperator} {$tagValue}");
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
     * Custom entity search filters.
     */
    protected function filterUpdatedAfter(EloquentBuilder $query, Entity $model, $input): void
    {
        try {
            $date = date_create($input);
            $query->where('updated_at', '>=', $date);
        } catch (\Exception $e) {
        }
    }

    protected function filterUpdatedBefore(EloquentBuilder $query, Entity $model, $input): void
    {
        try {
            $date = date_create($input);
            $query->where('updated_at', '<', $date);
        } catch (\Exception $e) {
        }
    }

    protected function filterCreatedAfter(EloquentBuilder $query, Entity $model, $input): void
    {
        try {
            $date = date_create($input);
            $query->where('created_at', '>=', $date);
        } catch (\Exception $e) {
        }
    }

    protected function filterCreatedBefore(EloquentBuilder $query, Entity $model, $input)
    {
        try {
            $date = date_create($input);
            $query->where('created_at', '<', $date);
        } catch (\Exception $e) {
        }
    }

    protected function filterCreatedBy(EloquentBuilder $query, Entity $model, $input)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $query->where('created_by', '=', $user->id);
        }
    }

    protected function filterUpdatedBy(EloquentBuilder $query, Entity $model, $input)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $query->where('updated_by', '=', $user->id);
        }
    }

    protected function filterOwnedBy(EloquentBuilder $query, Entity $model, $input)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $query->where('owned_by', '=', $user->id);
        }
    }

    protected function filterInName(EloquentBuilder $query, Entity $model, $input)
    {
        $query->where('name', 'like', '%' . $input . '%');
    }

    protected function filterInTitle(EloquentBuilder $query, Entity $model, $input)
    {
        $this->filterInName($query, $model, $input);
    }

    protected function filterInBody(EloquentBuilder $query, Entity $model, $input)
    {
        $query->where($model->textField, 'like', '%' . $input . '%');
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
     * Sorting filter options.
     */
    protected function sortByLastCommented(EloquentBuilder $query, Entity $model)
    {
        $commentsTable = DB::getTablePrefix() . 'comments';
        $morphClass = str_replace('\\', '\\\\', $model->getMorphClass());
        $commentQuery = DB::raw('(SELECT c1.entity_id, c1.entity_type, c1.created_at as last_commented FROM ' . $commentsTable . ' c1 LEFT JOIN ' . $commentsTable . ' c2 ON (c1.entity_id = c2.entity_id AND c1.entity_type = c2.entity_type AND c1.created_at < c2.created_at) WHERE c1.entity_type = \'' . $morphClass . '\' AND c2.created_at IS NULL) as comments');

        $query->join($commentQuery, $model->getTable() . '.id', '=', 'comments.entity_id')->orderBy('last_commented', 'desc');
    }
}
