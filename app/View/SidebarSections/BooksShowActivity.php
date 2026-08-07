<?php

namespace BookStack\View\SidebarSections;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Book;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksShowActivity extends SidebarSection
{
    protected string $view = 'books.parts.show-sidebar-section-activity';

    public function __construct(
        protected ActivityQueries $activityQueries,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'activity' => $this->activityQueries->entityActivity($book, 20, 1),
        ];
    }
}
