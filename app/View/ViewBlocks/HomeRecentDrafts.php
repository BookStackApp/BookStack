<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\PageQueries;
use BookStack\View\ViewBlockInterface;

class HomeRecentDrafts implements ViewBlockInterface
{
    public function __construct(
        protected PageQueries $pageQueries
    ) {
    }

    public function getLabel(): string
    {
        return trans('entities.my_recent_drafts');
    }

    public function getView(array $viewData): string
    {
        if ($viewData['homeView'] === 'default') {
            return 'home.parts.default-card-recent-drafts';
        }

        return 'home.parts.configured-section-recent-drafts';
    }

    public function withData(array $viewData): array
    {
        $draftPages = [];

        if (!user()->isGuest()) {
            $draftPages = $this->pageQueries->currentUserDraftsForList()
                ->orderBy('updated_at', 'desc')
                ->with('book')
                ->take(6)
                ->get();
        }

        return [
            'draftPages' => $draftPages,
        ];
    }
}
