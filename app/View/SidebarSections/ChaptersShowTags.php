<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Models\Chapter;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ChaptersShowTags extends SidebarSection
{
    protected string $view = 'chapters.parts.show-sidebar-section-tags';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Chapter $chapter */
        $chapter = $viewData['chapter'];

        return [
            'chapter' => $chapter,
        ];
    }
}
