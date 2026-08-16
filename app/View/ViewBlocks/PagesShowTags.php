<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Page;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class PagesShowTags extends ViewBlock
{
    protected static string $id = 'builtin_pages-show-tags';
    protected static string $view = 'pages.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function withData(array $viewData): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
        ];
    }
}
