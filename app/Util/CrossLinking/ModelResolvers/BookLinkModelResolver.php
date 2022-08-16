<?php

namespace BookStack\Util\CrossLinking\ModelResolvers;

use BookStack\Entities\Models\Book;
use BookStack\Model;

class BookLinkModelResolver implements CrossLinkModelResolver
{
    public function resolve(string $link): ?Model
    {
        $pattern = '/^' . preg_quote(url('/books'), '/') . '\/([\w-]+)' . '[#?\/$]/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $bookSlug = $matches[1];

        /** @var ?Book $model */
        $model = Book::query()->where('slug', '=',  $bookSlug)->first();

        return $model;
    }
}