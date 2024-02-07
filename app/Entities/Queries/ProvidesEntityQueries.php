<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Builder;

/**
 * Interface for our classes which provide common queries for our
 * entity objects. Ideally all queries for entities should run through
 * these classes.
 * Any added methods should return a builder instances to allow extension
 * via building on the query, unless the method starts with 'find'
 * in which case an entity object should be returned.
 * (nullable unless it's a *OrFail method).
 */
interface ProvidesEntityQueries
{
    public function start(): Builder;
    public function findVisibleById(int $id): ?Entity;
}
