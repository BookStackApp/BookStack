<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksIndexActions extends ViewBlock
{
    protected static string $id = 'builtin_books-index-actions';
    protected static string $view = 'books.parts.index-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        return [
            'view' => $viewData['view'],
        ];
    }
}
