<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\PageQueries;
use BookStack\View\BaseViewBlock;

class HomeRecentlyUpdatedPages extends BaseViewBlock
{
    public function __construct(
        protected PageQueries $queries
    ) {
    }

    public static function getId(): string
    {
        return 'builtin_home-recently-updated-pages';
    }

    public static function getLabel(): string
    {
        return trans('entities.recently_updated_pages');
    }

    public function getView(array $viewData): string
    {
        if ($viewData['homeView'] === 'default') {
            return 'home.parts.default-card-recently-updated-pages';
        }

        return 'home.parts.configured-section-recently-updated-pages';
    }

    public function getViewData(array $viewData): array
    {
        $recentlyUpdatedPages = $this->queries->visibleForList()
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        return [
            'recentlyUpdatedPages' => $recentlyUpdatedPages,
        ];
    }
}
