<?php namespace BookStack\Repos;

use BookStack\Tag;
use BookStack\Entity;
use BookStack\Services\PermissionService;

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
     * @param Tag $attr
     * @param Entity $ent
     * @param PermissionService $ps
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
     */
    public function getEntity($entityType, $entityId, $action = 'view')
    {
        $entityInstance = $this->entity->getEntityInstance($entityType);
        $searchQuery = $entityInstance->where('id', '=', $entityId)->with('tags');
        $searchQuery = $this->permissionService->enforceEntityRestrictions($searchQuery, $action);
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
        if ($entity === null) return collect();

        return $entity->tags;
    }

    /**
     * Get tag name suggestions from scanning existing tag names.
     * @param $searchTerm
     * @return array
     */
    public function getNameSuggestions($searchTerm)
    {
        if ($searchTerm === '') return [];
        $query = $this->tag->where('name', 'LIKE', $searchTerm . '%')->groupBy('name')->orderBy('name', 'desc');
        $query = $this->permissionService->filterRestrictedEntityRelations($query, 'tags', 'entity_id', 'entity_type');
        return $query->get(['name'])->pluck('name');
    }

    /**
     * Save an array of tags to an entity
     * @param Entity $entity
     * @param array $tags
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function saveTagsToEntity(Entity $entity, $tags = [])
    {
        $entity->tags()->delete();
        $newTags = [];
        foreach ($tags as $tag) {
            if (trim($tag['name']) === '') continue;
            $newTags[] = $this->newInstanceFromInput($tag);
        }

        return $entity->tags()->saveMany($newTags);
    }

    /**
     * Create a new Tag instance from user input.
     * @param $input
     * @return static
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