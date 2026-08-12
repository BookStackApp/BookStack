<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowActions extends ViewBlock
{
    protected string $id = 'builtin_books-show-actions';
    protected string $view = 'books.parts.show-sidebar-section-actions';
    protected string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'book' => $book,
            'watchOptions' => new UserEntityWatchOptions(user(), $book),
        ];
    }
}
