<?php

namespace BookStack\View\SidebarSections;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\References\ReferenceFetcher;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class BooksShowDetails extends SidebarSection
{
    protected string $view = 'books.parts.show-sidebar-section-details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        /** @var Book $book */
        $book = $viewData['book'];
        $referenceCount = $this->referenceFetcher->getReferenceCountToEntity($book);

        return [
            'book' => $book,
            'watchOptions' => new UserEntityWatchOptions(user(), $book),
            'referenceCount' => $referenceCount,
        ];
    }
}
