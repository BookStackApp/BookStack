<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ShelvesIndexNew extends ViewBlock
{
    protected string $id = 'builtin_shelves-index-new';
    protected string $view = 'shelves.parts.index-sidebar-section-new';
    protected string $labelTranslationKey = 'entities.shelves_new';

    public function __construct(
        protected BookshelfQueries $queries,
    ) {
    }

    public function withData(array $viewData): array
    {
        $new = $this->queries->visibleForList()
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return [
            'new' => $new,
        ];
    }
}
