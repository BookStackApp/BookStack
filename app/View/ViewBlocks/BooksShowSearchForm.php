<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowSearchForm extends ViewBlock
{
    protected static string $id = 'builtin_books-show-search-form';
    protected static string $view = 'entities.search-form';
    protected static string $labelTranslationKey = 'common.search';

    public function withData(array $viewData): array
    {
        return [
            'label' => trans('entities.books_search_this'),
        ];
    }
}
