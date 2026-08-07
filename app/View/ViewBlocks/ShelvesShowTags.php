<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesShowTags extends ViewBlock
{
    protected string $view = 'shelves.parts.show-sidebar-section-tags';

    public function withData(array $viewData, Request $request): array
    {
        return [
            'book' => $viewData['book'],
        ];
    }
}
