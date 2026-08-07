<?php

namespace BookStack\View\SidebarSections;

use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ShelvesIndexActions extends SidebarSection
{
    protected string $view = 'shelves.parts.index-sidebar-section-actions';

    public function withData(array $viewData, Request $request): array
    {
        return [
            'view' => $viewData['view'],
        ];
    }
}
