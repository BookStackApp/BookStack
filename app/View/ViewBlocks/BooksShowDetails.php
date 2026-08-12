<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\References\ReferenceFetcher;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class BooksShowDetails extends ViewBlock
{
    protected string $id = 'builtin_books-show-details';
    protected string $view = 'books.parts.show-sidebar-section-details';
    protected string $labelTranslationKey = 'common.details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function withData(array $viewData): array
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
