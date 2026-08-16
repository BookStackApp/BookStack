<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Bookshelf;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesShowActions extends ViewBlock
{
    protected static string $id = 'builtin_shelves-show-actions';
    protected static string $view = 'shelves.parts.show-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];

        return [
            'shelf' => $shelf,
        ];
    }
}
