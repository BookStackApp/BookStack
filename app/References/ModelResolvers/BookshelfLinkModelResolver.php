<?php

namespace BookStack\References\ModelResolvers;

use BookStack\App\Model;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Queries\BookshelfQueries;

class BookshelfLinkModelResolver implements CrossLinkModelResolver
{
    public function __construct(
        protected BookshelfQueries $queries
    ) {
    }
    public function resolve(string $link): ?Model
    {
        $pattern = '/^' . preg_quote(url('/shelves'), '/') . '\/([\w-]+)' . '([#?\/]|$)/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $shelfSlug = $matches[1];

        /** @var ?Bookshelf $model */
        $model = $this->queries->start()->where('slug', '=', $shelfSlug)->first(['id']);

        return $model;
    }
}
