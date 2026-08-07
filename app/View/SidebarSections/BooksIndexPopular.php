<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksIndexPopular extends SidebarSection
{
    protected string $view = 'books.parts.index-sidebar-section-popular';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        return [
            'popular' => $this->queries->popularForList()->take(4)->get(),
        ];
    }
}
