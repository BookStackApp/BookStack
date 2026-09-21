<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Queries\BookQueries;
use BookStack\View\SimpleViewBlock;

class BooksIndexNew extends SimpleViewBlock
{
    protected static string $id = 'builtin_books-index-new';
    protected static string $view = 'books.parts.index-sidebar-section-new';
    protected static string $labelTranslationKey = 'entities.books_new';

    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    public function getViewData(array $viewData): array
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
