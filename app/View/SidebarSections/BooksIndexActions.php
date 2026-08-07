<?php

namespace BookStack\View\SidebarSections;

use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksIndexActions extends SidebarSection
{
    protected string $view = 'books.parts.index-sidebar-section-actions';

    public function withData(array $viewData, Request $request): array
    {
        return [
            'view' => $viewData['view'],
        ];
    }
}
