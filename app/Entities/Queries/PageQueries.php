<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Builder;

class PageQueries
{
    public function start(): Builder
    {
        return Page::query();
    }

    public function visibleForList(): Builder
    {
        return $this->start()
            ->select(array_merge(Page::$listAttributes, ['book_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('books')
                    ->whereColumn('books.id', '=', 'pages.book_id');
            }]));
    }

    public function currentUserDraftsForList(): Builder
    {
        return $this->visibleForList()
            ->where('draft', '=', true)
            ->where('created_by', '=', user()->id);
    }
}
