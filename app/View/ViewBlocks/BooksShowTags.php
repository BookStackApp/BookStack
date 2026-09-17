<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowTags extends ViewBlock
{
    protected static string $id = 'builtin_books-show-tags';
    protected static string $view = 'books.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function withData(array $viewData): array
    {
        return [
            'book' => $viewData['book'],
        ];
    }
}
