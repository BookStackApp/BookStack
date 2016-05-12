<?php namespace BookStack\Repos;

use BookStack\Attribute;
use BookStack\Entity;
use BookStack\Services\PermissionService;

/**
 * Class AttributeRepo
 * @package BookStack\Repos
 */
class AttributeRepo
{

    protected $attribute;
    protected $entity;
    protected $permissionService;

    /**
     * AttributeRepo constructor.
     * @param Attribute $attr
     * @param Entity $ent
     * @param PermissionService $ps
     */
    public function __construct(Attribute $attr, Entity $ent, PermissionService $ps)
    {
        $this->attribute = $attr;
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
        $searchQuery = $entityInstance->where('id', '=', $entityId)->with('attributes');
        $searchQuery = $this->permissionService->enforceEntityRestrictions($searchQuery, $action);
        return $searchQuery->first();
    }

    /**
     * Get all attributes for a particular entity.
     * @param string $entityType
     * @param int $entityId
     * @return mixed
     */
    public function getForEntity($entityType, $entityId)
    {
        $entity = $this->getEntity($entityType, $entityId);
        if ($entity === null) return collect();

        return $entity->attributes;
    }

    /**
     * Get attribute name suggestions from scanning existing attribute names.
     * @param $searchTerm
     * @return array
     */
    public function getNameSuggestions($searchTerm)
    {
        if ($searchTerm === '') return [];
        $query = $this->attribute->where('name', 'LIKE', $searchTerm . '%')->groupBy('name')->orderBy('name', 'desc');
        $query = $this->permissionService->filterRestrictedEntityRelations($query, 'attributes', 'entity_id', 'entity_type');
        return $query->get(['name'])->pluck('name');
    }

    /**
     * Save an array of attributes to an entity
     * @param Entity $entity
     * @param array $attributes
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function saveAttributesToEntity(Entity $entity, $attributes = [])
    {
        $entity->attributes()->delete();
        $newAttributes = [];
        foreach ($attributes as $attribute) {
            if (trim($attribute['name']) === '') continue;
            $newAttributes[] = $this->newInstanceFromInput($attribute);
        }

        return $entity->attributes()->saveMany($newAttributes);
    }

    /**
     * Create a new Attribute instance from user input.
     * @param $input
     * @return static
     */
    protected function newInstanceFromInput($input)
    {
        $name = trim($input['name']);
        $value = isset($input['value']) ? trim($input['value']) : '';
        // Any other modification or cleanup required can go here
        $values = ['name' => $name, 'value' => $value];
        return $this->attribute->newInstance($values);
    }

}