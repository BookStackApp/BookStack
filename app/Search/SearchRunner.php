<?php

namespace BookStack\Search;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Search\Options\TagSearchOption;
use BookStack\Users\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use WeakMap;

class SearchRunner
{
    /**
     * Retain a cache of score adjusted terms for specific search options.
     */
    protected WeakMap $termAdjustmentCache;

    public function __construct(
        protected EntityProvider $entityProvider,
        protected PermissionApplicator $permissions,
        protected EntityQueries $entityQueries,
    ) {
        $this->termAdjustmentCache = new WeakMap();
    }

    /**
     * Search all entities in the system.
     * The provided count is for each entity to search,
     * Total returned could be larger and not guaranteed.
     *
     * @return array{total: int, count: int, has_more: bool, results: Collection<Entity>}
     */
    public function searchEntities(SearchOptions $searchOpts, string $entityType = 'all', int $page = 1, int $count = 20): array
    {
        $entityTypes = array_keys($this->entityProvider->all());
        $entityTypesToSearch = $entityTypes;

        $filterMap = $searchOpts->filters->toValueMap();
        if ($entityType !== 'all') {
            $entityTypesToSearch = [$entityType];
        } elseif (isset($filterMap['type'])) {
            $entityTypesToSearch = explode('|', $filterMap['type']);
        }

        $results = collect();
        $total = 0;
        $hasMore = false;

        foreach ($entityTypesToSearch as $entityType) {
            if (!in_array($entityType, $entityTypes)) {
                continue;
            }

            $searchQuery = $this->buildQuery($searchOpts, $entityType);
            $entityTotal = $searchQuery->count();
            $searchResults = $this->getPageOfDataFromQuery($searchQuery, $entityType, $page, $count);

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
        $filterMap = $opts->filters->toValueMap();
        $entityTypesToSearch = isset($filterMap['type']) ? explode('|', $filterMap['type']) : $entityTypes;

        $results = collect();
        foreach ($entityTypesToSearch as $entityType) {
            if (!in_array($entityType, $entityTypes)) {
                continue;
            }

            $search = $this->buildQuery($opts, $entityType)->where('book_id', '=', $bookId)->take(20)->get();
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
        $pages = $this->buildQuery($opts, 'page')->where('chapter_id', '=', $chapterId)->take(20)->get();

        return $pages->sortByDesc('score');
    }

    /**
     * Get a page of result data from the given query based on the provided page parameters.
     */
    protected function getPageOfDataFromQuery(EloquentBuilder $query, string $entityType, int $page = 1, int $count = 20): EloquentCollection
    {
        $relations = ['tags'];

        if ($entityType === 'page' || $entityType === 'chapter') {
            $relations['book'] = function (BelongsTo $query) {
                $query->scopes('visible');
            };
        }

        if ($entityType === 'page') {
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
    protected function buildQuery(SearchOptions $searchOpts, string $entityType): EloquentBuilder
    {
        $entityModelInstance = $this->entityProvider->get($entityType);
        $entityQuery = $this->entityQueries->visibleForList($entityType);

        // Handle normal search terms
        $this->applyTermSearch($entityQuery, $searchOpts, $entityType);

        // Handle exact term matching
        foreach ($searchOpts->exacts->all() as $exact) {
            $filter = function (EloquentBuilder $query) use ($exact, $entityModelInstance) {
                $inputTerm = str_replace('\\', '\\\\', $exact->value);
                $query->where('name', 'like', '%' . $inputTerm . '%')
                    ->orWhere($entityModelInstance->textField, 'like', '%' . $inputTerm . '%');
            };

            $exact->negated ? $entityQuery->whereNot($filter) : $entityQuery->where($filter);
        }

        // Handle tag searches
        foreach ($searchOpts->tags->all() as $tagOption) {
            $this->applyTagSearch($entityQuery, $tagOption);
        }

        // Handle filters
        foreach ($searchOpts->filters->all() as $filterOption) {
            $functionName = Str::camel('filter_' . $filterOption->getKey());
            if (method_exists($this, $functionName)) {
                $this->$functionName($entityQuery, $entityModelInstance, $filterOption->value, $filterOption->negated);
            }
        }

        return $entityQuery;
    }

    /**
     * For the given search query, apply the queries for handling the regular search terms.
     */
    protected function applyTermSearch(EloquentBuilder $entityQuery, SearchOptions $options, string $entityType): void
    {
        $terms = $options->searches->toValueArray();
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

        $subQuery->where('entity_type', '=', $entityType);
        $subQuery->where(function (Builder $query) use ($terms) {
            foreach ($terms as $inputTerm) {
                $escapedTerm = str_replace('\\', '\\\\', $inputTerm);
                $query->orWhere('term', 'like', $escapedTerm . '%');
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

        foreach ($options->searches->toValueArray() as $term) {
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
     * Apply a tag search term onto an entity query.
     */
    protected function applyTagSearch(EloquentBuilder $query, TagSearchOption $option): void
    {
        $filter = function (EloquentBuilder $query) use ($option): void {
            $tagParts = $option->getParts();
            if (empty($tagParts['operator']) || empty($tagParts['value'])) {
                $query->where('name', '=', $tagParts['name']);
                return;
            }

            if (!empty($tagParts['name'])) {
                $query->where('name', '=', $tagParts['name']);
            }

            if (is_numeric($tagParts['value']) && $tagParts['operator'] !== 'like') {
                // We have to do a raw sql query for this since otherwise PDO will quote the value and MySQL will
                // search the value as a string which prevents being able to do number-based operations
                // on the tag values. We ensure it has a numeric value and then cast it just to be sure.
                /** @var Connection $connection */
                $connection = $query->getConnection();
                $quotedValue = (float) trim($connection->getPdo()->quote($tagParts['value']), "'");
                $query->whereRaw("value {$tagParts['operator']} {$quotedValue}");
            } else if ($tagParts['operator'] === 'like') {
                $query->where('value', $tagParts['operator'], str_replace('\\', '\\\\', $tagParts['value']));
            } else {
                $query->where('value', $tagParts['operator'], $tagParts['value']);
            }
        };

        $option->negated ? $query->whereDoesntHave('tags', $filter) : $query->whereHas('tags', $filter);
    }

    protected function applyNegatableWhere(EloquentBuilder $query, bool $negated, string $column, string $operator, mixed $value): void
    {
        if ($negated) {
            $query->whereNot($column, $operator, $value);
        } else {
            $query->where($column, $operator, $value);
        }
    }

    /**
     * Custom entity search filters.
     */
    protected function filterUpdatedAfter(EloquentBuilder $query, Entity $model, string $input, bool $negated): void
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'updated_at', '>=', $date);
    }

    protected function filterUpdatedBefore(EloquentBuilder $query, Entity $model, string $input, bool $negated): void
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'updated_at', '<', $date);
    }

    protected function filterCreatedAfter(EloquentBuilder $query, Entity $model, string $input, bool $negated): void
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'created_at', '>=', $date);
    }

    protected function filterCreatedBefore(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'created_at', '<', $date);
    }

    protected function filterCreatedBy(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $this->applyNegatableWhere($query, $negated, 'created_by', '=', $user->id);
        }
    }

    protected function filterUpdatedBy(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $this->applyNegatableWhere($query, $negated, 'updated_by', '=', $user->id);
        }
    }

    protected function filterOwnedBy(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $this->applyNegatableWhere($query, $negated, 'owned_by', '=', $user->id);
        }
    }

    protected function filterInName(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $this->applyNegatableWhere($query, $negated, 'name', 'like', '%' . $input . '%');
    }

    protected function filterInTitle(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $this->filterInName($query, $model, $input, $negated);
    }

    protected function filterInBody(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $this->applyNegatableWhere($query, $negated, $model->textField, 'like', '%' . $input . '%');
    }

    protected function filterIsRestricted(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $negated ? $query->whereDoesntHave('permissions') : $query->whereHas('permissions');
    }

    protected function filterViewedByMe(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $filter = function ($query) {
            $query->where('user_id', '=', user()->id);
        };

        $negated ? $query->whereDoesntHave('views', $filter) : $query->whereHas('views', $filter);
    }

    protected function filterNotViewedByMe(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $filter = function ($query) {
            $query->where('user_id', '=', user()->id);
        };

        $negated ? $query->whereHas('views', $filter) : $query->whereDoesntHave('views', $filter);
    }

    protected function filterIsTemplate(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        if ($model instanceof Page) {
            $this->applyNegatableWhere($query, $negated, 'template', '=', true);
        }
    }

    protected function filterSortBy(EloquentBuilder $query, Entity $model, string $input, bool $negated)
    {
        $functionName = Str::camel('sort_by_' . $input);
        if (method_exists($this, $functionName)) {
            $this->$functionName($query, $model, $negated);
        }
    }

    /**
     * Sorting filter options.
     */
    protected function sortByLastCommented(EloquentBuilder $query, Entity $model, bool $negated)
    {
        $commentsTable = DB::getTablePrefix() . 'comments';
        $morphClass = str_replace('\\', '\\\\', $model->getMorphClass());
        $commentQuery = DB::raw('(SELECT c1.entity_id, c1.entity_type, c1.created_at as last_commented FROM ' . $commentsTable . ' c1 LEFT JOIN ' . $commentsTable . ' c2 ON (c1.entity_id = c2.entity_id AND c1.entity_type = c2.entity_type AND c1.created_at < c2.created_at) WHERE c1.entity_type = \'' . $morphClass . '\' AND c2.created_at IS NULL) as comments');

        $query->join($commentQuery, $model->getTable() . '.id', '=', DB::raw('comments.entity_id'))
            ->orderBy('last_commented', $negated ? 'asc' : 'desc');
    }
}
