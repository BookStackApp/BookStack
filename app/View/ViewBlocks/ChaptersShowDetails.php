<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\References\ReferenceFetcher;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ChaptersShowDetails extends ViewBlock
{
    protected string $view = 'chapters.parts.show-sidebar-section-details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function withData(array $viewData): array
    {
        /** @var Book $book */
        $book = $viewData['book'];
        /** @var Chapter $chapter */
        $chapter = $viewData['chapter'];

        $referenceCount = $this->referenceFetcher->getReferenceCountToEntity($chapter);

        return [
            'chapter' => $chapter,
            'book' => $book,
            'watchOptions' => new UserEntityWatchOptions(user(), $chapter),
            'referenceCount' => $referenceCount,
        ];
    }
}
