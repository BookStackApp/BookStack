<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;

class MixedEntityRequestHelper
{
    public function __construct(
        protected EntityProvider $entities,
    ) {
    }

    /**
     * Query out an entity, visible to the current user, for the given
     * entity request details (this provided in a request validated by
     * this classes' validationRules method).
     * @param array{type: string, id: string} $requestData
     */
    public function getVisibleEntityFromRequestData(array $requestData): Entity
    {
        $entityType = $this->entities->get($requestData['type']);

        return $entityType->newQuery()->scopes(['visible'])->findOrFail($requestData['id']);
    }

    /**
     * Get the validation rules for an abstract entity request.
     * @return array{type: string[], id: string[]}
     */
    public function validationRules(): array
    {
        return [
                'type' => ['required', 'string'],
                'id'   => ['required', 'integer'],
        ];
    }
}
