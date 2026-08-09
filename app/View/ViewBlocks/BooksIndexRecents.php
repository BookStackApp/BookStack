<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksIndexRecents extends ViewBlock
{
    protected string $view = 'books.parts.index-sidebar-section-recents';
    protected string $labelTranslationKey = 'entities.recently_viewed';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function withData(array $viewData): array
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
