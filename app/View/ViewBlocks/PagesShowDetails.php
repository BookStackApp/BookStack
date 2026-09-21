<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\References\ReferenceFetcher;
use BookStack\View\SimpleViewBlock;

class PagesShowDetails extends SimpleViewBlock
{
    protected static string $id = 'builtin_pages-show-details';
    protected static string $view = 'pages.parts.show-sidebar-section-details';
    protected static string $labelTranslationKey = 'common.details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function getViewData(array $viewData): array
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
