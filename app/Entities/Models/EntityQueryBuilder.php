<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class EntityQueryBuilder extends Builder
{
    /**
     * Create a new Eloquent query builder instance.
     */
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);

        $this->withGlobalScope('entity', new EntityScope());
    }

    public function withoutGlobalScope($scope): static
    {
        // Prevent removal of the entity scope
        if ($scope === 'entity') {
            return $this;
        }

        return parent::withoutGlobalScope($scope);
    }

    /**
     * Override the default forceDelete method to add type filter onto the query
     * since it specifically ignores scopes by default.
     */
    public function forceDelete()
    {
        return $this->query->where('type', '=', $this->model->getMorphClass())->delete();
    }
}
