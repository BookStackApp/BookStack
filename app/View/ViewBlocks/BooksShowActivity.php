<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Book;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowActivity extends ViewBlock
{
    protected string $view = 'books.parts.show-sidebar-section-activity';
    protected string $labelTranslationKey = 'entities.recent_activity';

    public function __construct(
        protected ActivityQueries $activityQueries,
    ) {
    }

    public function withData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'activity' => $this->activityQueries->entityActivity($book, 20, 1),
        ];
    }
}
