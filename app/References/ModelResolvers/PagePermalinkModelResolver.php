<?php

namespace BookStack\References\ModelResolvers;

use BookStack\Entities\Models\Page;
use BookStack\Model;

class PagePermalinkModelResolver implements CrossLinkModelResolver
{
    public function resolve(string $link): ?Model
    {
        $pattern = '/^' . preg_quote(url('/link'), '/') . '\/(\d+)/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $id = intval($matches[1]);
        /** @var ?Page $model */
        $model = Page::query()->find($id, ['id']);

        return $model;
    }
}