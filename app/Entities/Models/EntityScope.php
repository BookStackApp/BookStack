<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\JoinClause;

class EntityScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder = $builder->where('type', '=', $model->getMorphClass());
        if ($model instanceof Page) {
            $builder->leftJoin('entity_page_contents', 'entity_page_contents.page_id', '=', 'entities.id');
        } else {
            $builder->leftJoin('entity_container_contents', function (JoinClause $join) use ($model) {
                $join->on('entity_container_contents.entity_id', '=', 'entities.id')
                    ->where('entity_container_contents.entity_type', '=', $model->getMorphClass());
            });
        }
    }
}
