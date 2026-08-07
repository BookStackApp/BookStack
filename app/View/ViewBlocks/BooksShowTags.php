<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowTags extends ViewBlock
{
    protected string $view = 'books.parts.show-sidebar-section-tags';

    public function withData(array $viewData, Request $request): array
    {
        return [
            'book' => $viewData['book'],
        ];
    }
}
