<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\SimpleViewBlock;

class BooksIndexActions extends SimpleViewBlock
{
    protected static string $id = 'builtin_books-index-actions';
    protected static string $view = 'books.parts.index-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function getViewData(array $viewData): array
    {
        return [
            'view' => $viewData['view'],
        ];
    }
}
