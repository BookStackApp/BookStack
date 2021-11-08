<?php

namespace BookStack\Entities\Tools;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchRunner
{
    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * Acceptable operators to be used in a query.
     *
     * @var array
     */
    protected $queryOperators = ['<=', '>=', '=', '<', '>', 'like', '!='];

    public function __construct(EntityProvider $entityProvider, PermissionService $permissionService)
    {
        $this->entityProvider = $entityProvider;
        $this->permissionService = $permissionService;
    }

    /**
     * Search all entities in the system.
     * The provided count is for each entity to search,
     * Total returned could be larger and not guaranteed.
     */
    public function searchEntities(SearchOptions $searchOpts, string $entityType = 'all', int $page = 1, int $count = 20, string $action = 'view'): array
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

            $searchQuery = $this->buildQuery($searchOpts, $entityType, $action);
            $entityTotal = $searchQuery->count();
            $searchResults = $this->getPageOfDataFromQuery($searchQuery, $page, $count);

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
    protected function getPageOfDataFromQuery(EloquentBuilder $query, int $page = 1, int $count = 20): EloquentCollection
    {
        return $query->clone()
            ->skip(($page - 1) * $count)
            ->take($count)
            ->get();
    }

    /**
     * Create a search query for an entity.
     */
    protected function buildQuery(SearchOptions $searchOpts, string $entityType = 'page', string $action = 'view'): EloquentBuilder
    {
        $entity = $this->entityProvider->get($entityType);
        $entitySelect = $entity->newQuery();

        // Handle normal search terms
        if (count($searchOpts->searches) > 0) {
            $rawScoreSum = DB::raw('SUM(score) as score');
            $subQuery = DB::table('search_terms')->select('entity_id', 'entity_type', $rawScoreSum);
            $subQuery->where('entity_type', '=', $entity->getMorphClass());
            $subQuery->where(function (Builder $query) use ($searchOpts) {
                foreach ($searchOpts->searches as $inputTerm) {
                    $query->orWhere('term', 'like', $inputTerm . '%');
                }
            })->groupBy('entity_type', 'entity_id');
            $entitySelect->join(DB::raw('(' . $subQuery->toSql() . ') as s'), function (JoinClause $join) {
                $join->on('id', '=', 'entity_id');
            })->addSelect($entity->getTable() . '.*')
                ->selectRaw('s.score')
                ->orderBy('score', 'desc');
            $entitySelect->mergeBindings($subQuery);
        }

        // Handle exact term matching
        foreach ($searchOpts->exacts as $inputTerm) {
            $entitySelect->where(function (EloquentBuilder $query) use ($inputTerm, $entity) {
                $query->where('name', 'like', '%' . $inputTerm . '%')
                    ->orWhere($entity->textField, 'like', '%' . $inputTerm . '%');
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

        return $this->permissionService->enforceEntityRestrictions($entity, $entitySelect, $action);
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
     * Custom entity search filters.
     */
    protected function filterUpdatedAfter(EloquentBuilder $query, Entity $model, $input): void
    {
        try {
            $date = date_create($input);
            $query->where('updated_at', '>=', $date);
        } catch (\Exception $e) {}
    }

    protected function filterUpdatedBefore(EloquentBuilder $query, Entity $model, $input): void
    {
        try {
            $date = date_create($input);
            $query->where('updated_at', '<', $date);
        } catch (\Exception $e) {}
    }

    protected function filterCreatedAfter(EloquentBuilder $query, Entity $model, $input): void
    {
        try {
            $date = date_create($input);
            $query->where('created_at', '>=', $date);
        } catch (\Exception $e) {}
    }

    protected function filterCreatedBefore(EloquentBuilder $query, Entity $model, $input)
    {
        try {
            $date = date_create($input);
            $query->where('created_at', '<', $date);
        } catch (\Exception $e) {}
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
