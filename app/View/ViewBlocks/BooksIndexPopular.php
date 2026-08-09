<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksIndexPopular extends ViewBlock
{
    protected string $view = 'books.parts.index-sidebar-section-popular';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function withData(array $viewData): array
    {
        return [
            'popular' => $this->queries->popularForList()->take(4)->get(),
        ];
    }
}
