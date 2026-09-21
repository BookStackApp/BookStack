<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\SimpleViewBlock;

class BooksShowSearchForm extends SimpleViewBlock
{
    protected static string $id = 'builtin_books-show-search-form';
    protected static string $view = 'entities.search-form';
    protected static string $labelTranslationKey = 'common.search';

    public function getViewData(array $viewData): array
    {
        return [
            'label' => trans('entities.books_search_this'),
        ];
    }
}
