<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class HomeActions extends ViewBlock
{
    protected string $view = 'home.parts.configured-section-actions';

    public function withData(array $viewData): array
    {
        return [
            'view' => $viewData['view'] ?? '',
            'homeView' => $viewData['homeView'] ?? 'default',
        ];
    }
}
