<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksIndexNew extends ViewBlock
{
    protected string $view = 'books.parts.index-sidebar-section-new';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function withData(array $viewData): array
    {
        $new = $this->queries->visibleForList()
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return [
            'new' => $new,
        ];
    }
}
