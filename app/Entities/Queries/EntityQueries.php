<?php

namespace BookStack\Entities\Queries;

class EntityQueries
{
    public function __construct(
        public BookshelfQueries $shelves,
        public BookQueries $books,
        public PageQueries $pages,
    ) {
    }
}
