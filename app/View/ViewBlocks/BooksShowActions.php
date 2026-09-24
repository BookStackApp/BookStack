<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\View\SimpleViewBlock;

class BooksShowActions extends SimpleViewBlock
{
    protected static string $id = 'builtin_books-show-actions';
    protected static string $view = 'books.parts.show-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function getViewData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'book' => $book,
            'watchOptions' => new UserEntityWatchOptions(user(), $book),
        ];
    }
}
