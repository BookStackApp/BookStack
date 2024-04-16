<?php

namespace BookStack\References\ModelResolvers;

use BookStack\App\Model;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Queries\ChapterQueries;

class ChapterLinkModelResolver implements CrossLinkModelResolver
{
    public function __construct(
        protected ChapterQueries $queries
    ) {
    }

    public function resolve(string $link): ?Model
    {
        $pattern = '/^' . preg_quote(url('/books'), '/') . '\/([\w-]+)' . '\/chapter\/' . '([\w-]+)' . '([#?\/]|$)/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $bookSlug = $matches[1];
        $chapterSlug = $matches[2];

        /** @var ?Chapter $model */
        $model = $this->queries->usingSlugs($bookSlug, $chapterSlug)->first(['id']);

        return $model;
    }
}
