<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;

class HomeActions extends ViewBlock
{
    protected static string $id = 'builtin_home-actions';
    protected static string $view = 'home.parts.configured-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        return [
            'view' => $viewData['view'] ?? '',
            'homeView' => $viewData['homeView'] ?? 'default',
        ];
    }
}
