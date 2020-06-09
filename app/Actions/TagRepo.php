<?php namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Entity;

/**
 * Class TagRepo
 * @package BookStack\Repos
 */
class TagRepo
{

    protected $tag;
    protected $entity;
    protected $permissionService;

    /**
     * TagRepo constructor.
     * @param \BookStack\Actions\Tag $attr
     * @param \BookStack\Entities\Entity $ent
     * @param \BookStack\Auth\Permissions\PermissionService $ps
     */
    public function __construct(Tag $attr, Entity $ent, PermissionService $ps)
    {
        $this->tag = $attr;
        $this->entity = $ent;
        $this->permissionService = $ps;
    }

    /**
     * Get an entity instance of its particular type.
     * @param $entityType
     * @param $entityId
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getEntity($entityType, $entityId, $action = 'view')
    {
        $entityInstance = $this->entity->getEntityInstance($entityType);
        $searchQuery = $entityInstance->where('id', '=', $entityId)->with('tags');
        $searchQuery = $this->permissionService->enforceEntityRestrictions($entityType, $searchQuery, $action);
        return $searchQuery->first();
    }

    /**
     * Get all tags for a particular entity.
     * @param string $entityType
     * @param int $entityId
     * @return mixed
     */
    public function getForEntity($entityType, $entityId)
    {
        $entity = $this->getEntity($entityType, $entityId);
        if ($entity === null) {
            return collect();
        }

        return $entity->tags;
    }

    /**
     * Get tag name suggestions from scanning existing tag names.
     * If no search term is given the 50 most popular tag names are provided.
     * @param $searchTerm
     * @return array
     */
    public function getNameSuggestions($searchTerm = false)
    {
        $query = $this->tag->select('*', \DB::raw('count(*) as count'))->groupBy('name');

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
     * @param $searchTerm
     * @param $tagName
     * @return array
     */
    public function getValueSuggestions($searchTerm = false, $tagName = false)
    {
        $query = $this->tag->select('*', \DB::raw('count(*) as count'))->groupBy('value');

        if ($searchTerm) {
            $query = $query->where('value', 'LIKE', $searchTerm . '%')->orderBy('value', 'desc');
        } else {
            $query = $query->orderBy('count', 'desc')->take(50);
        }

        if ($tagName !== false) {
            $query = $query->where('name', '=', $tagName);
        }

        $query = $this->permissionService->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');
        return $query->get(['value'])->pluck('value');
    }

    /**
     * Save an array of tags to an entity
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function saveTagsToEntity(Entity $entity, array $tags = [])
    {
        $entity->tags()->delete();
        $newTags = [];

        foreach ($tags as $tag) {
            if (trim($tag['name']) === '') {
                continue;
            }
            $newTags[] = $this->newInstanceFromInput($tag);
        }

        return $entity->tags()->saveMany($newTags);
    }

    /**
     * Create a new Tag instance from user input.
     * @param $input
     * @return \BookStack\Actions\Tag
     */
    protected function newInstanceFromInput($input)
    {
        $name = trim($input['name']);
        $value = isset($input['value']) ? trim($input['value']) : '';
        // Any other modification or cleanup required can go here
        $values = ['name' => $name, 'value' => $value];
        return $this->tag->newInstance($values);
    }
}
