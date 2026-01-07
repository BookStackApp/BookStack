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
        $table = $model->getTable();
        if ($model instanceof Page) {
            $builder->leftJoin('entity_page_data', 'entity_page_data.page_id', '=', "{$table}.id");
        } else {
            $builder->leftJoin('entity_container_data', function (JoinClause $join) use ($model, $table) {
                $join->on('entity_container_data.entity_id', '=', "{$table}.id")
                    ->where('entity_container_data.entity_type', '=', $model->getMorphClass());
            });
        }
    }
}
