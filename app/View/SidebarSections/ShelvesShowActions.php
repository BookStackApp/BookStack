<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Models\Bookshelf;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ShelvesShowActions extends SidebarSection
{
    protected string $view = 'shelves.parts.show-sidebar-section-actions';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];

        return [
            'shelf' => $shelf,
        ];
    }
}
