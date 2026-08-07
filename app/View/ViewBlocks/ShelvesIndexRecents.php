<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesIndexRecents extends ViewBlock
{
    protected string $view = 'shelves.parts.index-sidebar-section-recents';

    public function __construct(
        protected BookshelfQueries $queries,
    ) {
    }

    public function withData(array $viewData, Request $request): array
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
