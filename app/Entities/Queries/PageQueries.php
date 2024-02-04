<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Builder;

class PageQueries
{
    public static function start(): Builder
    {
        return Page::query();
    }

    public static function visibleForList(): Builder
    {
        return Page::visible()
            ->select(array_merge(Page::$listAttributes, ['book_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('books')
                    ->whereColumn('books.id', '=', 'pages.book_id');
            }]));
    }

    public static function currentUserDraftsForList(): Builder
    {
        return static::visibleForList()
            ->where('draft', '=', true)
            ->where('created_by', '=', user()->id);
    }
}
