<?php

namespace BookStack\Util\CrossLinking\ModelResolvers;

use BookStack\Entities\Models\Chapter;
use BookStack\Model;

class ChapterLinkModelResolver implements CrossLinkModelResolver
{
    public function resolve(string $link): ?Model
    {
        $pattern = '/^' . preg_quote(url('/books'), '/') . '\/([\w-]+)' . '\/chapter\/' . '([\w-]+)' . '[#?\/$]/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $bookSlug = $matches[1];
        $chapterSlug = $matches[2];

        /** @var ?Chapter $model */
        $model = Chapter::query()->whereSlugs($bookSlug, $chapterSlug)->first();

        return $model;
    }
}