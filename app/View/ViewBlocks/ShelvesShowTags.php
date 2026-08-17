<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesShowTags extends ViewBlock
{
    protected static string $id = 'builtin_shelves-show-tags';
    protected static string $view = 'shelves.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function withData(array $viewData): array
    {
        return [
            'shelf' => $viewData['shelf'],
        ];
    }
}
