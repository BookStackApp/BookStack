<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Models\Book;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksShowShelves extends SidebarSection
{
    protected string $view = 'books.parts.show-sidebar-section-shelves';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Book $book */
        $book = $viewData['book'];
        $shelves = $book->shelves()->scopes('visible')->get();

        return [
            'shelves' => $shelves,
        ];
    }
}
