<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowActions extends ViewBlock
{
    protected string $view = 'books.parts.show-sidebar-section-actions';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'book' => $book,
            'watchOptions' => new UserEntityWatchOptions(user(), $book),
        ];
    }
}
