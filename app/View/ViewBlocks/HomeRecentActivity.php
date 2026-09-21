<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\ActivityQueries;
use BookStack\View\BaseViewBlock;

class HomeRecentActivity extends BaseViewBlock
{
    public function __construct(
        protected ActivityQueries $activityQueries
    ) {
    }

    public static function getId(): string
    {
        return 'builtin_home-recent-activity';
    }

    public static function getLabel(): string
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

    public function getViewData(array $viewData): array
    {
        $activity = $this->activityQueries->latest(10);
        return [
            'activity' => $activity,
        ];
    }
}
