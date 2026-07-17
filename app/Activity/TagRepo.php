<?php

namespace BookStack\Activity;

use BookStack\Activity\Models\Tag;
use BookStack\Entities\Models\Entity;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Util\SimpleListOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TagRepo
{
    public function __construct(
        protected PermissionApplicator $permissions
    ) {
    }

    /**
     * Start a query against all tags in the system, with total counts for their usage,
     * suitable for a system interface list with listing options.
     */
    public function queryWithTotalsForList(SimpleListOptions $listOptions, string $nameFilter): Builder
    {
        $searchTerm = $listOptions->getSearch();
        $sort = $listOptions->getSort();
        if ($sort === 'name' && $nameFilter) {
            $sort = 'value';
        }

        $query = $this->baseQueryWithTotals($nameFilter, $searchTerm)
            ->orderBy($sort, $listOptions->getOrder());

        return $this->permissions->restrictEntityRelationQuery($query, 'tags', 'entity_id', 'entity_type');
    }

    /**
     * Start a query against all tags in the system, with total counts for their usage,
     * which can be used via the API.
     */
    public function queryWithTotalsForApi(string $nameFilter): Builder
    {
        $query = $this->baseQueryWithTotals($nameFilter, '');
        return $this->permissions->restrictEntityRelationQuery($query, 'tags', 'entity_id', 'entity_type');
    }

    protected function baseQueryWithTotals(string $nameFilter, string $searchTerm): Builder
    {
        $query = Tag::query()
            ->select([
                'name',
                ($searchTerm || $nameFilter) ? 'value' : DB::raw('COUNT(distinct value) as `values`'),
                DB::raw('COUNT(id) as usages'),
                DB::raw('CAST(SUM(IF(entity_type = \'page\', 1, 0)) as UNSIGNED) as page_count'),
                DB::raw('CAST(SUM(IF(entity_type = \'chapter\', 1, 0)) as UNSIGNED) as chapter_count'),
                DB::raw('CAST(SUM(IF(entity_type = \'book\', 1, 0)) as UNSIGNED) as book_count'),
                DB::raw('CAST(SUM(IF(entity_type = \'bookshelf\', 1, 0)) as UNSIGNED) as shelf_count'),
            ])
            ->whereHas('entity');

        if ($nameFilter) {
            $query->where('name', '=', $nameFilter);
            $query->groupBy('value');
        } elseif ($searchTerm) {
            $query->groupBy('name', 'value');
        } else {
            $query->groupBy('name');
        }

        if ($searchTerm) {
            $query->where(function (Builder $query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('value', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query;
    }

    /**
     * Get tag name suggestions from scanning existing tag names.
     * If no search term is given the 50 most popular tag names are provided.
     */
    public function getNameSuggestions(string $searchTerm): Collection
    {
        $query = Tag::query()
            ->select('*', DB::raw('count(*) as count'))
            ->groupBy('name');

        if ($searchTerm) {
            $query = $query->where('name', 'LIKE', $searchTerm . '%')->orderBy('name', 'asc');
        } else {
            $query = $query->orderBy('count', 'desc')->take(50);
        }

        $query = $this->permissions->restrictEntityRelationQuery($query, 'tags', 'entity_id', 'entity_type');

        return $query->pluck('name');
    }

    /**
     * Get tag value suggestions from scanning existing tag values.
     * If no search is given the 50 most popular values are provided.
     * Passing a tagName will only find values for a tags with a particular name.
     */
    public function getValueSuggestions(string $searchTerm, string $tagName): Collection
    {
        $query = Tag::query()
            ->select('*', DB::raw('count(*) as count'))
            ->where('value', '!=', '')
            ->groupBy('value');

        if ($searchTerm) {
            $query = $query->where('value', 'LIKE', $searchTerm . '%')->orderBy('value', 'desc');
        } else {
            $query = $query->orderBy('count', 'desc')->take(50);
        }

        if ($tagName) {
            $query = $query->where('name', '=', $tagName);
        }

        $query = $this->permissions->restrictEntityRelationQuery($query, 'tags', 'entity_id', 'entity_type');

        return $query->pluck('value');
    }

    /**
     * Save an array of tags to an entity.
     */
    public function saveTagsToEntity(Entity $entity, array $tags = []): iterable
    {
        $entity->tags()->delete();

        $newTags = collect($tags)->filter(function ($tag) {
            return boolval(trim($tag['name']));
        })->map(function ($tag) {
            return $this->newInstanceFromInput($tag);
        })->all();

        return $entity->tags()->saveMany($newTags);
    }

    /**
     * Create a new Tag instance from user input.
     * Input must be an array with a 'name' and an optional 'value' key.
     */
    protected function newInstanceFromInput(array $input): Tag
    {
        return new Tag([
            'name'  => trim($input['name']),
            'value' => trim($input['value'] ?? ''),
        ]);
    }
}
