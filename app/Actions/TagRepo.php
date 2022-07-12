<?php

namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TagRepo
{
    protected PermissionApplicator $permissions;

    public function __construct(PermissionApplicator $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Start a query against all tags in the system.
     */
    public function queryWithTotals(string $searchTerm, string $nameFilter): Builder
    {
        $query = Tag::query()
            ->select([
                'name',
                ($searchTerm || $nameFilter) ? 'value' : DB::raw('COUNT(distinct value) as `values`'),
                DB::raw('COUNT(id) as usages'),
                DB::raw('SUM(IF(entity_type = \'page\', 1, 0)) as page_count'),
                DB::raw('SUM(IF(entity_type = \'chapter\', 1, 0)) as chapter_count'),
                DB::raw('SUM(IF(entity_type = \'book\', 1, 0)) as book_count'),
                DB::raw('SUM(IF(entity_type = \'bookshelf\', 1, 0)) as shelf_count'),
            ])
            ->orderBy($nameFilter ? 'value' : 'name');

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

        return $this->permissions->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');
    }

    /**
     * Get tag name suggestions from scanning existing tag names.
     * If no search term is given the 50 most popular tag names are provided.
     */
    public function getNameSuggestions(?string $searchTerm): Collection
    {
        $query = Tag::query()
            ->select('*', DB::raw('count(*) as count'))
            ->groupBy('name');

        if ($searchTerm) {
            $query = $query->where('name', 'LIKE', $searchTerm . '%')->orderBy('name', 'desc');
        } else {
            $query = $query->orderBy('count', 'desc')->take(50);
        }

        $query = $this->permissions->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');

        return $query->get(['name'])->pluck('name');
    }

    /**
     * Get tag value suggestions from scanning existing tag values.
     * If no search is given the 50 most popular values are provided.
     * Passing a tagName will only find values for a tags with a particular name.
     */
    public function getValueSuggestions(?string $searchTerm, ?string $tagName): Collection
    {
        $query = Tag::query()
            ->select('*', DB::raw('count(*) as count'))
            ->groupBy('value');

        if ($searchTerm) {
            $query = $query->where('value', 'LIKE', $searchTerm . '%')->orderBy('value', 'desc');
        } else {
            $query = $query->orderBy('count', 'desc')->take(50);
        }

        if ($tagName) {
            $query = $query->where('name', '=', $tagName);
        }

        $query = $this->permissions->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');

        return $query->get(['value'])->pluck('value');
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
