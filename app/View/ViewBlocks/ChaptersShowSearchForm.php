<?php

namespace BookStack\View\ViewBlocks;

use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ChaptersShowSearchForm extends ViewBlock
{
    protected string $view = 'entities.search-form';
    protected string $labelTranslationKey = 'common.search';

    public function withData(array $viewData): array
    {
        return [
            'label' => trans('entities.chapters_search_this'),
        ];
    }
}
