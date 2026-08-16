<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Book;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowShelves extends ViewBlock
{
    protected static string $id = 'builtin_books-show-shelves';
    protected static string $view = 'books.parts.show-sidebar-section-shelves';
    protected static string $labelTranslationKey = 'entities.shelves';

    public function withData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];
        $shelves = $book->shelves()->scopes('visible')->get();

        return [
            'shelves' => $shelves,
        ];
    }
}
