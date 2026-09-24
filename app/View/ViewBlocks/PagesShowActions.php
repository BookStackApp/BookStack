<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Page;
use BookStack\View\SimpleViewBlock;

class PagesShowActions extends SimpleViewBlock
{
    protected static string $id = 'builtin_pages-show-actions';
    protected static string $view = 'pages.parts.show-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function getViewData(array $viewData): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
            'watchOptions' => new UserEntityWatchOptions(user(), $page),
        ];
    }
}
