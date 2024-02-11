<?php

namespace BookStack\References\ModelResolvers;

use BookStack\App\Model;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Queries\BookQueries;

class BookLinkModelResolver implements CrossLinkModelResolver
{
    public function __construct(
        protected BookQueries $queries
    ) {
    }

    public function resolve(string $link): ?Model
    {
        $pattern = '/^' . preg_quote(url('/books'), '/') . '\/([\w-]+)' . '([#?\/]|$)/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $bookSlug = $matches[1];

        /** @var ?Book $model */
        $model = $this->queries->start()->where('slug', '=', $bookSlug)->first(['id']);

        return $model;
    }
}
