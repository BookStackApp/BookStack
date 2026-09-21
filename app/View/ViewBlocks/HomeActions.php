<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\SimpleViewBlock;

class HomeActions extends SimpleViewBlock
{
    protected static string $id = 'builtin_home-actions';
    protected static string $view = 'home.parts.configured-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function getViewData(array $viewData): array
    {
        return [
            'view' => $viewData['view'] ?? '',
            'homeView' => $viewData['homeView'] ?? 'default',
        ];
    }
}
