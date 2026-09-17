<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Book;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class PagesShowBookTree extends ViewBlock
{
    protected static string $id = 'builtin_pages-show-book-tree';
    protected static string $view = 'entities.book-tree';
    protected static string $labelTranslationKey = 'entities.books_navigation';

    public function withData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'book' => $book,
            'bookTree' => $viewData['bookTree'],
        ];
    }
}
