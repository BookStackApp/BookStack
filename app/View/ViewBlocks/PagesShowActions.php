<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Page;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class PagesShowActions extends ViewBlock
{
    protected string $view = 'pages.parts.show-sidebar-section-actions';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
            'watchOptions' => new UserEntityWatchOptions(user(), $page),
        ];
    }
}
