<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Bookshelf;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesShowActions extends ViewBlock
{
    protected string $view = 'shelves.parts.show-sidebar-section-actions';
    protected string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];

        return [
            'shelf' => $shelf,
        ];
    }
}
