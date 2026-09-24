<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Book;
use BookStack\View\SimpleViewBlock;

class ChaptersShowBookTree extends SimpleViewBlock
{
    protected static string $id = 'builtin_chapters-show-book-tree';
    protected static string $view = 'entities.book-tree';
    protected static string $labelTranslationKey = 'entities.books_navigation';

    public function getViewData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'book' => $book,
            'bookTree' => $viewData['bookTree'],
        ];
    }
}
