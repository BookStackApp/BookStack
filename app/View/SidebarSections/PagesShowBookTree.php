<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Models\Book;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class PagesShowBookTree extends SidebarSection
{
    protected string $view = 'entities.book-tree';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Book $book */
        $book = $viewData['book'];

        return [
            'book' => $book,
            'bookTree' => $viewData['bookTree'],
        ];
    }
}
