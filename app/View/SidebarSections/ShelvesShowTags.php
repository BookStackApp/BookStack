<?php

namespace BookStack\View\SidebarSections;

use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ShelvesShowTags extends SidebarSection
{
    protected string $view = 'shelves.parts.show-sidebar-section-tags';

    public function withData(array $viewData, Request $request): array
    {
        return [
            'book' => $viewData['book'],
        ];
    }
}
