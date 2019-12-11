<?php

namespace BookStack\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait GroupedLastScope
{
    /**
     * Get the latest entry for each group.
     *
     * Each group is composed of one or more columns that make a unique combination to return the
     * last entry for.
     *
     * @param Builder $query
     * @param array|null $fields A list of fields that's considered as a unique entry by the query.
     *
     * @return Builder
     */
    public function scopeLastPerGroup(Builder $query, ?array $fields = null) : Builder
    {
        return $query->whereIn('id', function (QueryBuilder $query) use ($fields) {
            return $query->from(static::getTable())
                ->selectRaw('max(`id`)')
                ->groupBy($fields ?? static::$groupedLastScopeFields);
        });
    }
}