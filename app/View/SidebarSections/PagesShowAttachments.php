<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Models\Page;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class PagesShowAttachments extends SidebarSection
{
    protected string $view = 'pages.parts.show-sidebar-section-attachments';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
        ];
    }
}
