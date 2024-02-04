<?php

namespace BookStack\Entities\Queries;

class EntityQueries
{
    public function __construct(
        public BookQueries $books,
        public PageQueries $pages,
    ) {
    }
}
