<?php

namespace BookStack\View\SidebarSections;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Bookshelf;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ShelvesShowActivity extends SidebarSection
{
    protected string $view = 'shelves.parts.show-sidebar-section-activity';

    public function __construct(
        protected ActivityQueries $activityQueries,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];

        return [
            'activity' => $this->activityQueries->entityActivity($shelf, 20, 1),
        ];
    }
}
