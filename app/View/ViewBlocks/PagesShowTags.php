<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Page;
use BookStack\View\SimpleViewBlock;

class PagesShowTags extends SimpleViewBlock
{
    protected static string $id = 'builtin_pages-show-tags';
    protected static string $view = 'pages.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function getViewData(array $viewData): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
        ];
    }
}
