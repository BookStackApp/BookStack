<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\View\SimpleViewBlock;

class ShelvesIndexPopular extends SimpleViewBlock
{
    protected static string $id = 'builtin_shelves-index-popular';
    protected static string $view = 'shelves.parts.index-sidebar-section-popular';
    protected static string $labelTranslationKey = 'entities.shelves_popular';

    public function __construct(
        protected BookshelfQueries $queries,
    ) {
    }

    public function getViewData(array $viewData): array
    {
        return [
            'popular' => $this->queries->popularForList()->take(4)->get(),
        ];
    }
}
