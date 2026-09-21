<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Bookshelf;
use BookStack\View\SimpleViewBlock;

class ShelvesShowActions extends SimpleViewBlock
{
    protected static string $id = 'builtin_shelves-show-actions';
    protected static string $view = 'shelves.parts.show-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function getViewData(array $viewData): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];

        return [
            'shelf' => $shelf,
        ];
    }
}
