<?php

namespace BookStack\View\SidebarSections;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\References\ReferenceFetcher;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class PagesShowDetails extends SidebarSection
{
    protected string $view = 'pages.parts.show-sidebar-section-details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        /** @var Book $book */
        $book = $viewData['book'];
        /** @var Page $page */
        $page = $viewData['page'];

        $referenceCount = $this->referenceFetcher->getReferenceCountToEntity($page);

        return [
            'page' => $page,
            'book' => $book,
            'watchOptions' => new UserEntityWatchOptions(user(), $page),
            'referenceCount' => $referenceCount,
        ];
    }
}
