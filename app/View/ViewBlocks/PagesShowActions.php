<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Page;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class PagesShowActions extends ViewBlock
{
    protected string $view = 'pages.parts.show-sidebar-section-actions';
    protected string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
            'watchOptions' => new UserEntityWatchOptions(user(), $page),
        ];
    }
}
