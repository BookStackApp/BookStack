<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksIndexRecents extends SidebarSection
{
    protected string $view = 'books.parts.index-sidebar-section-recents';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        $userSignedIn = !user()->isGuest();
        $recents = $userSignedIn ? $this->queries->recentlyViewedForCurrentUser()->take(4)->get() : null;
        return [
            'recents' => $recents,
        ];
    }
}
