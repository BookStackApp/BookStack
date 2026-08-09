<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\ActivityQueries;
use BookStack\View\ViewBlockInterface;
use Illuminate\Http\Request;

class HomeRecentActivity implements ViewBlockInterface
{
    public function __construct(
        protected ActivityQueries $activityQueries
    ) {
    }

    public function getLabel(): string
    {
        return trans('entities.recent_activity');
    }

    public function getView(array $viewData): string
    {
        if ($viewData['homeView'] === 'default') {
            return 'home.parts.default-card-recent-activity';
        }

        return 'home.parts.configured-section-recent-activity';
    }

    public function withData(array $viewData): array
    {
        $activity = $this->activityQueries->latest(10);
        return [
            'activity' => $activity,
        ];
    }
}
