<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Queries\QueryRecentlyViewed;
use BookStack\Entities\Queries\QueryTopFavourites;
use BookStack\View\ViewBlockInterface;
use Illuminate\Http\Request;

class HomeRecentlyViewedOrRecentBooks implements ViewBlockInterface
{
    public function __construct(
        protected EntityQueries $queries,
        protected QueryRecentlyViewed $recentlyViewed,
    ) {
    }

    public function getView(array $viewData): string
    {
        if ($viewData['homeView'] === 'default') {
            return 'home.parts.default-card-recently-viewed-or-recent-books';
        }

        return 'home.parts.configured-section-recently-viewed-or-recent-books';
    }

    public function withData(array $viewData): array
    {
        if (user()->isGuest()) {
            $recents = $this->queries->books->visibleForList()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        } else {
            $recents = $this->recentlyViewed->run(10, 1);
        }

        return [
            'recents' => $recents
        ];
    }
}
