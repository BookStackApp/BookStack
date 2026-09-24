<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\View\SimpleViewBlock;

class ShelvesIndexRecents extends SimpleViewBlock
{
    protected static string $id = 'builtin_shelves-index-recents';
    protected static string $view = 'shelves.parts.index-sidebar-section-recents';
    protected static string $labelTranslationKey = 'entities.recently_viewed';

    public function __construct(
        protected BookshelfQueries $queries,
    ) {
    }

    public function getViewData(array $viewData): array
    {
        $recents = null;
        if (!user()->isGuest()) {
            $recents = $this->queries->recentlyViewedForCurrentUser()->take(4)->get();
        }

        return [
            'recents' => $recents,
        ];
    }
}
