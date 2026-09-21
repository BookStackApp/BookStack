<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\SimpleViewBlock;

class BooksShowTags extends SimpleViewBlock
{
    protected static string $id = 'builtin_books-show-tags';
    protected static string $view = 'books.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function getViewData(array $viewData): array
    {
        return [
            'book' => $viewData['book'],
        ];
    }
}
