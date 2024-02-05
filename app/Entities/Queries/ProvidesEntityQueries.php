<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Builder;

interface ProvidesEntityQueries
{
    public function start(): Builder;
    public function findVisibleById(int $id): ?Entity;
}
