<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksIndexNew extends SidebarSection
{
    protected string $view = 'books.parts.index-sidebar-section-new';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function withData(array $viewData, Request $request): array
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
