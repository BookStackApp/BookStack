<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Book;
use BookStack\View\SimpleViewBlock;

class BooksShowActivity extends SimpleViewBlock
{
    protected static string $id = 'builtin_books-show-activity';
    protected static string $view = 'books.parts.show-sidebar-section-activity';
    protected static string $labelTranslationKey = 'entities.recent_activity';

    public function __construct(
        protected ActivityQueries $activityQueries,
    ) {
    }

    public function getViewData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'activity' => $this->activityQueries->entityActivity($book, 20, 1),
        ];
    }
}
