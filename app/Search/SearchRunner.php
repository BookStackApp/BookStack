<?php

namespace BookStack\Search;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Tools\EntityHydrator;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Search\Options\TagSearchOption;
use BookStack\Users\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use WeakMap;

class SearchRunner
{
    /**
     * Retain a cache of score-adjusted terms for specific search options.
     */
    protected WeakMap $termAdjustmentCache;

    public function __construct(
        protected EntityProvider $entityProvider,
        protected PermissionApplicator $permissions,
        protected EntityQueries $entityQueries,
        protected EntityHydrator $entityHydrator,
        protected SearchCache $searchCache,
    ) {
        $this->termAdjustmentCache = new WeakMap();
    }

    /**
     * Search all entities in the system.
     *
     * @return array{total: int, results: Collection<Entity>}
     */
    public function searchEntities(SearchOptions $searchOpts, string $entityType = 'all', int $page = 1, int $count = 20): array
    {
        $cacheKey = $this->generateCacheKey($searchOpts, $entityType, $page, $count);
        
        // 尝试从缓存获取结果
        if ($cached = $this->searchCache->getSearchResults($cacheKey)) {
            return $cached;
        }

        $entityTypes = array_keys($this->entityProvider->all());
        $entityTypesToSearch = $entityTypes;

        $filterMap = $searchOpts->filters->toValueMap();
        if ($entityType !== 'all') {
            $entityTypesToSearch = [$entityType];
        } elseif (isset($filterMap['type'])) {
            $entityTypesToSearch = explode('|', $filterMap['type']);
        }

        // 使用优化的查询构建
        $searchQuery = $this->buildOptimizedQuery($searchOpts, $entityTypesToSearch);
        
        // 使用分页查询减少内存使用
        $total = $searchQuery->clone()->count();
        $results = $this->getPageOfDataFromQuery($searchQuery, $page, $count);

        $result = [
            'total'    => $total,
            'results'  => $results->values(),
        ];

        // 缓存结果
        $this->searchCache->cacheSearchResults($cacheKey, $result);

        return $result;
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

        $filteredTypes = array_intersect($entityTypesToSearch, $entityTypes);
        $query = $this->buildQuery($opts, $filteredTypes)->where('book_id', '=', $bookId);

        return $this->getPageOfDataFromQuery($query, 1, 20)->sortByDesc('score');
    }

    /**
     * Search a chapter for entities.
     */
    public function searchChapter(int $chapterId, string $searchString): Collection
    {
        $opts = SearchOptions::fromString($searchString);
        $query = $this->buildQuery($opts, ['page'])->where('chapter_id', '=', $chapterId);

        return $this->getPageOfDataFromQuery($query, 1, 20)->sortByDesc('score');
    }

    /**
     * Build an optimized search query.
     * @param string[] $entityTypes
     */
    protected function buildOptimizedQuery(SearchOptions $searchOpts, array $entityTypes): EloquentBuilder
    {
        $entityQuery = $this->entityQueries->visibleForList()
            ->select('entities.*')
            ->whereIn('type', $entityTypes)
            ->with(['book:id,name', 'chapter:id,name']) // 预加载关联数据，避免N+1查询
            ->limit(1000); // 限制结果集大小

        // 处理正常搜索词
        $this->applyTermSearch($entityQuery, $searchOpts, $entityTypes);

        // 处理精确匹配 - 使用索引优化的查询
        foreach ($searchOpts->exacts->all() as $exact) {
            $this->applyExactMatch($entityQuery, $exact);
        }

        // 处理标签搜索
        foreach ($searchOpts->tags->all() as $tagOption) {
            $this->applyTagSearchOptimized($entityQuery, $tagOption);
        }

        // 处理过滤器
        foreach ($searchOpts->filters->all() as $filterOption) {
            $this->applyFilterOptimized($entityQuery, $filterOption);
        }

        return $entityQuery;
    }

    /**
     * Apply optimized exact match search.
     */
    protected function applyExactMatch(EloquentBuilder $query, $exact): void
    {
        $inputTerm = str_replace('\\', '\\\\', $exact->value);
        
        // 使用全文搜索或索引优化的LIKE查询
        $filter = function (EloquentBuilder $query) use ($inputTerm) {
            $query->where(function ($q) use ($inputTerm) {
                $q->where('name', 'like', $inputTerm . '%') // 前缀匹配使用索引
                  ->orWhere('name', '=', $inputTerm) // 精确匹配
                  ->orWhere('description', 'like', $inputTerm . '%')
                  ->orWhere('text', 'like', $inputTerm . '%');
            });
        };

        $exact->negated ? $query->whereNot($filter) : $query->where($filter);
    }

    /**
     * Apply optimized tag search.
     */
    protected function applyTagSearchOptimized(EloquentBuilder $query, $tagOption): void
    {
        // 使用EXISTS子查询替代JOIN，提高性能
        $query->whereExists(function ($subQuery) use ($tagOption) {
            $subQuery->select(DB::raw(1))
                ->from('tags')
                ->whereColumn('tags.entity_id', 'entities.id')
                ->whereColumn('tags.entity_type', 'entities.type')
                ->where('tags.name', '=', $tagOption->name);

            if ($tagOption->value !== null) {
                $subQuery->where('tags.value', '=', $tagOption->value);
            }
        });
    }

    /**
     * Apply optimized filter.
     */
    protected function applyFilterOptimized(EloquentBuilder $query, $filterOption): void
    {
        $functionName = Str::camel('filter_' . $filterOption->getKey());
        if (method_exists($this, $functionName)) {
            // 使用优化的过滤器方法
            $optimizedMethod = $functionName . 'Optimized';
            if (method_exists($this, $optimizedMethod)) {
                $this->$optimizedMethod($query, $filterOption->value, $filterOption->negated);
            } else {
                $this->$functionName($query, $filterOption->value, $filterOption->negated);
            }
        }
    }

    /**
     * Get a page of result data from the given query based on the provided page parameters.
     */
    protected function getPageOfDataFromQuery(EloquentBuilder $query, int $page, int $count): Collection
    {
        $entities = $query->clone()
            ->skip(($page - 1) * $count)
            ->take($count)
            ->get();

        $hydrated = $this->entityHydrator->hydrate($entities->all(), true, true);

        return collect($hydrated);
    }

    /**
     * Create a search query for an entity.
     * @param string[] $entityTypes
     */
    protected function buildQuery(SearchOptions $searchOpts, array $entityTypes): EloquentBuilder
    {
        $entityQuery = $this->entityQueries->visibleForList()
            ->whereIn('type', $entityTypes);

        // Handle normal search terms
        $this->applyTermSearch($entityQuery, $searchOpts, $entityTypes);

        // Handle exact term matching
        foreach ($searchOpts->exacts->all() as $exact) {
            $filter = function (EloquentBuilder $query) use ($exact) {
                $inputTerm = str_replace('\\', '\\\\', $exact->value);
                $query->where('name', 'like', '%' . $inputTerm . '%')
                    ->orWhere('description', 'like', '%' . $inputTerm . '%')
                    ->orWhere('text', 'like', '%' . $inputTerm . '%');
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
                $this->$functionName($entityQuery, $filterOption->value, $filterOption->negated);
            }
        }

        return $entityQuery;
    }

    /**
     * For the given search query, apply the queries for handling the regular search terms.
     */
    protected function applyTermSearch(EloquentBuilder $entityQuery, SearchOptions $options, array $entityTypes): void
    {
        $terms = $options->searches->toValueArray();
        if (count($terms) === 0) {
            return;
        }

        $scoredTerms = $this->getTermAdjustments($options);
        $scoreSelect = $this->selectForScoredTerms($scoredTerms);

        // 创建优化的子查询，限制结果集大小
        $subQuery = DB::table('search_terms')->select([
            'entity_id',
            'entity_type',
            DB::raw($scoreSelect['statement']),
        ]);

        $subQuery->addBinding($scoreSelect['bindings'], 'select');
        $subQuery->where(function (Builder $query) use ($terms) {
            foreach ($terms as $inputTerm) {
                $escapedTerm = strtolower(str_replace('\\', '\\\\', $inputTerm));
                // 使用索引优化的前缀匹配
                $query->orWhere('term', 'like', $escapedTerm . '%');
            }
        });
        $subQuery->groupBy('entity_type', 'entity_id')
                 ->having(DB::raw($scoreSelect['statement']), '>', 0.1) // 过滤低分结果
                 ->limit(1000); // 限制结果集大小

        // 使用优化的JOIN方式
        $entityQuery->joinSub($subQuery, 's', function (JoinClause $join) {
            $join->on('s.entity_id', '=', 'entities.id')
                ->on('s.entity_type', '=', 'entities.type');
        });
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
     * @return array<string, float>
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

    protected function applyNegatableWhere(EloquentBuilder $query, bool $negated, string|callable $column, string|null $operator, mixed $value): void
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
    protected function filterUpdatedAfter(EloquentBuilder $query, string $input, bool $negated): void
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'updated_at', '>=', $date);
    }

    protected function filterUpdatedBefore(EloquentBuilder $query, string $input, bool $negated): void
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'updated_at', '<', $date);
    }

    protected function filterCreatedAfter(EloquentBuilder $query, string $input, bool $negated): void
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'created_at', '>=', $date);
    }

    protected function filterCreatedBefore(EloquentBuilder $query, string $input, bool $negated)
    {
        $date = date_create($input);
        $this->applyNegatableWhere($query, $negated, 'created_at', '<', $date);
    }

    protected function filterCreatedBy(EloquentBuilder $query, string $input, bool $negated)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $this->applyNegatableWhere($query, $negated, 'created_by', '=', $user->id);
        }
    }

    protected function filterUpdatedBy(EloquentBuilder $query, string $input, bool $negated)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $this->applyNegatableWhere($query, $negated, 'updated_by', '=', $user->id);
        }
    }

    protected function filterOwnedBy(EloquentBuilder $query, string $input, bool $negated)
    {
        $userSlug = $input === 'me' ? user()->slug : trim($input);
        $user = User::query()->where('slug', '=', $userSlug)->first(['id']);
        if ($user) {
            $this->applyNegatableWhere($query, $negated, 'owned_by', '=', $user->id);
        }
    }

    protected function filterInName(EloquentBuilder $query, string $input, bool $negated)
    {
        $this->applyNegatableWhere($query, $negated, 'name', 'like', '%' . $input . '%');
    }

    protected function filterInTitle(EloquentBuilder $query, string $input, bool $negated)
    {
        $this->filterInName($query, $input, $negated);
    }

    protected function filterInBody(EloquentBuilder $query, string $input, bool $negated)
    {
        $this->applyNegatableWhere($query, $negated, function (EloquentBuilder $query) use ($input) {
            $query->where('description', 'like', '%' . $input . '%')
                ->orWhere('text', 'like', '%' . $input . '%');
        }, null, null);
    }

    protected function filterIsRestricted(EloquentBuilder $query, string $input, bool $negated)
    {
        $negated ? $query->whereDoesntHave('permissions') : $query->whereHas('permissions');
    }

    protected function filterViewedByMe(EloquentBuilder $query, string $input, bool $negated)
    {
        $filter = function ($query) {
            $query->where('user_id', '=', user()->id);
        };

        $negated ? $query->whereDoesntHave('views', $filter) : $query->whereHas('views', $filter);
    }

    protected function filterNotViewedByMe(EloquentBuilder $query, string $input, bool $negated)
    {
        $filter = function ($query) {
            $query->where('user_id', '=', user()->id);
        };

        $negated ? $query->whereHas('views', $filter) : $query->whereDoesntHave('views', $filter);
    }

    protected function filterIsTemplate(EloquentBuilder $query, string $input, bool $negated)
    {
        $this->applyNegatableWhere($query, $negated, 'template', '=', true);
    }

    protected function filterSortBy(EloquentBuilder $query, string $input, bool $negated)
    {
        $functionName = Str::camel('sort_by_' . $input);
        if (method_exists($this, $functionName)) {
            $this->$functionName($query, $negated);
        }
    }

    /**
     * Sorting filter options.
     */
    protected function sortByLastCommented(EloquentBuilder $query, bool $negated)
    {
        $commentsTable = DB::getTablePrefix() . 'comments';
        $commentQuery = DB::raw('(SELECT c1.commentable_id, c1.commentable_type, c1.created_at as last_commented FROM ' . $commentsTable . ' c1 LEFT JOIN ' . $commentsTable . ' c2 ON (c1.commentable_id = c2.commentable_id AND c1.commentable_type = c2.commentable_type AND c1.created_at < c2.created_at) WHERE c2.created_at IS NULL) as comments');

        $query->join($commentQuery, function (JoinClause $join) {
            $join->on('entities.id', '=', 'comments.commentable_id')
                ->on('entities.type', '=', 'comments.commentable_type');
        })->orderBy('last_commented', $negated ? 'asc' : 'desc');
    }

    /**
     * Generate cache key for search results.
     */
    protected function generateCacheKey(SearchOptions $searchOpts, string $entityType, int $page, int $count): string
    {
        $keyData = [
            'search' => $searchOpts->toString(),
            'type' => $entityType,
            'page' => $page,
            'count' => $count,
            'user_id' => user()->id ?? 0,
        ];

        return md5(serialize($keyData));
    }

    /**
     * Optimized filter for updated after date.
     */
    protected function filterUpdatedAfterOptimized(EloquentBuilder $query, string $value, bool $negated): void
    {
        $date = $this->parseDateFromString($value);
        if ($date) {
            $negated ? $query->where('updated_at', '<', $date) : $query->where('updated_at', '>=', $date);
        }
    }

    /**
     * Optimized filter for created by user.
     */
    protected function filterCreatedByOptimized(EloquentBuilder $query, string $value, bool $negated): void
    {
        $user = User::query()->where('email', '=', $value)->first(['id']);
        if ($user) {
            $negated ? $query->where('created_by', '!=', $user->id) : $query->where('created_by', '=', $user->id);
        }
    }

    /**
     * Parse date from string with improved error handling.
     */
    protected function parseDateFromString(string $value): ?Carbon
    {
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return Carbon::createFromFormat('Y-m-d', $value);
            }
            return Carbon::parse($value);
        } catch (Exception $e) {
            return null;
        }
    }
