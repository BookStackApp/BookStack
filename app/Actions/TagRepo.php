<?php namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Models\Entity;
use DB;
use Illuminate\Support\Collection;

class TagRepo
{

    protected $tag;
    protected $permissionService;

    /**
     * TagRepo constructor.
     */
    public function __construct(Tag $tag, PermissionService $ps)
    {
        $this->tag = $tag;
        $this->permissionService = $ps;
    }

    /**
     * Get tag name suggestions from scanning existing tag names.
     * If no search term is given the 50 most popular tag names are provided.
     */
    public function getNameSuggestions(?string $searchTerm): Collection
    {
        $query = $this->tag->newQuery()
            ->select('*', DB::raw('count(*) as count'))
            ->groupBy('name');

        if ($searchTerm) {
            $query = $query->where('name', 'LIKE', $searchTerm . '%')->orderBy('name', 'desc');
        } else {
            $query = $query->orderBy('count', 'desc')->take(50);
        }

        $query = $this->permissionService->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');
        return $query->get(['name'])->pluck('name');
    }

    /**
     * Get tag value suggestions from scanning existing tag values.
     * If no search is given the 50 most popular values are provided.
     * Passing a tagName will only find values for a tags with a particular name.
     */
    public function getValueSuggestions(?string $searchTerm, ?string $tagName): Collection
    {
        $query = $this->tag->newQuery()
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

        $query = $this->permissionService->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');
        return $query->get(['value'])->pluck('value');
    }

    /**
     * Save an array of tags to an entity
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
        $name = trim($input['name']);
        $value = isset($input['value']) ? trim($input['value']) : '';
        return $this->tag->newInstance(['name' => $name, 'value' => $value]);
    }
}
