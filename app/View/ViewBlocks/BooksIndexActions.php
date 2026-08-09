<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksIndexActions extends ViewBlock
{
    protected string $view = 'books.parts.index-sidebar-section-actions';
    protected string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        return [
            'view' => $viewData['view'],
        ];
    }
}
