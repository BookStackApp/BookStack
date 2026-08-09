<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowSearchForm extends ViewBlock
{
    protected string $view = 'entities.search-form';

    public function withData(array $viewData): array
    {
        return [
            'label' => trans('entities.books_search_this'),
        ];
    }
}
