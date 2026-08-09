<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesIndexPopular extends ViewBlock
{
    protected string $view = 'shelves.parts.index-sidebar-section-popular';

    public function __construct(
        protected BookshelfQueries $queries,
    ) {
    }

    public function withData(array $viewData): array
    {
        return [
            'popular' => $this->queries->popularForList()->take(4)->get(),
        ];
    }
}
