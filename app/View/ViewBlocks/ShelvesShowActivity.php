<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Bookshelf;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesShowActivity extends ViewBlock
{
    protected string $view = 'shelves.parts.show-sidebar-section-activity';
    protected string $labelTranslationKey = 'entities.recent_activity';

    public function __construct(
        protected ActivityQueries $activityQueries,
    ) {
    }

    public function withData(array $viewData): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];

        return [
            'activity' => $this->activityQueries->entityActivity($shelf, 20, 1),
        ];
    }
}
