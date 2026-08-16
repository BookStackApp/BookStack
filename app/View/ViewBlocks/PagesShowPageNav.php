<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class PagesShowPageNav extends ViewBlock
{
    protected static string $id = 'builtin_pages-show-page-nav';
    protected static string $view = 'pages.parts.show-sidebar-section-page-nav';
    protected static string $labelTranslationKey = 'entities.pages_navigation';

    public function withData(array $viewData): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        $pageContent = new PageContent($page);
        $pageNav = $pageContent->getNavigation($page->html);

        return [
            'pageNav' => $pageNav,
        ];
    }
}
