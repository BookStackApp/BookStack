<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\SimpleViewBlock;

class ShelvesShowTags extends SimpleViewBlock
{
    protected static string $id = 'builtin_shelves-show-tags';
    protected static string $view = 'shelves.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function getViewData(array $viewData): array
    {
        return [
            'shelf' => $viewData['shelf'],
        ];
    }
}
