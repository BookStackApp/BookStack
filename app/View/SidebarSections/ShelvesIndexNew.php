<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ShelvesIndexNew extends SidebarSection
{
    protected string $view = 'shelves.parts.index-sidebar-section-new';

    public function __construct(
        protected BookshelfQueries $queries,
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
