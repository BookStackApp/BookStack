<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowTags extends ViewBlock
{
    protected string $id = 'builtin_books-show-tags';
    protected string $view = 'books.parts.show-sidebar-section-tags';
    protected string $labelTranslationKey = 'entities.tags';

    public function withData(array $viewData): array
    {
        return [
            'book' => $viewData['book'],
        ];
    }
}
