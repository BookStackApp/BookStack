<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesIndexActions extends ViewBlock
{
    protected string $id = 'builtin_shelves-index-actions';
    protected string $view = 'shelves.parts.index-sidebar-section-actions';
    protected string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        return [
            'view' => $viewData['view'],
        ];
    }
}
