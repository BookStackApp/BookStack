<?php

namespace BookStack\View\SidebarSections;

use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ChaptersShowSearchForm extends SidebarSection
{
    protected string $view = 'entities.search-form';

    public function withData(array $viewData, Request $request): array
    {
        return [
            'label' => trans('entities.chapters_search_this'),
        ];
    }
}
