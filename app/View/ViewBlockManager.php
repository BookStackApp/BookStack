<?php

namespace BookStack\View;

use BookStack\View\ViewBlocks\BooksIndexActions;
use BookStack\View\ViewBlocks\BooksIndexNew;
use BookStack\View\ViewBlocks\BooksIndexPopular;
use BookStack\View\ViewBlocks\BooksIndexRecents;
use BookStack\View\ViewBlocks\BooksShowActions;
use BookStack\View\ViewBlocks\BooksShowActivity;
use BookStack\View\ViewBlocks\BooksShowDetails;
use BookStack\View\ViewBlocks\BooksShowSearchForm;
use BookStack\View\ViewBlocks\BooksShowShelves;
use BookStack\View\ViewBlocks\BooksShowTags;
use BookStack\View\ViewBlocks\ChaptersShowActions;
use BookStack\View\ViewBlocks\ChaptersShowBookTree;
use BookStack\View\ViewBlocks\ChaptersShowDetails;
use BookStack\View\ViewBlocks\ChaptersShowSearchForm;
use BookStack\View\ViewBlocks\ChaptersShowTags;
use BookStack\View\ViewBlocks\PagesShowActions;
use BookStack\View\ViewBlocks\PagesShowAttachments;
use BookStack\View\ViewBlocks\PagesShowBookTree;
use BookStack\View\ViewBlocks\PagesShowDetails;
use BookStack\View\ViewBlocks\PagesShowPageNav;
use BookStack\View\ViewBlocks\PagesShowTags;
use BookStack\View\ViewBlocks\ShelvesIndexActions;
use BookStack\View\ViewBlocks\ShelvesIndexNew;
use BookStack\View\ViewBlocks\ShelvesIndexPopular;
use BookStack\View\ViewBlocks\ShelvesIndexRecents;
use BookStack\View\ViewBlocks\ShelvesShowActions;
use BookStack\View\ViewBlocks\ShelvesShowActivity;
use BookStack\View\ViewBlocks\ShelvesShowDetails;
use BookStack\View\ViewBlocks\ShelvesShowTags;
use Illuminate\Contracts\Container\BindingResolutionException;

class ViewBlockManager
{
    /**
     * @var array<string, array<string, class-string<ViewBlockInterface>[]>>
     */
    protected array $blocksByLocationAndPosition = [
        'shelves-index' => [
            'left' => [
                ShelvesIndexRecents::class,
                ShelvesIndexPopular::class,
                ShelvesIndexNew::class,
            ],
            'right' => [
                ShelvesIndexActions::class,
            ],
        ],
        'shelves-show' => [
            'left' => [
                ShelvesShowTags::class,
                ShelvesShowDetails::class,
                ShelvesShowActivity::class,
            ],
            'right' => [
                ShelvesShowActions::class,
            ],
        ],
        'books-index' => [
            'left' => [
                BooksIndexRecents::class,
                BooksIndexPopular::class,
                BooksIndexNew::class,
            ],
            'right' => [
                BooksIndexActions::class,
            ],
        ],
        'books-show' => [
            'left' => [
                BooksShowSearchForm::class,
                BooksShowTags::class,
                BooksShowShelves::class,
                BooksShowActivity::class,
            ],
            'right' => [
                BooksShowDetails::class,
                BooksShowActions::class,
            ],
        ],
        'chapters-show' => [
            'left' => [
                ChaptersShowSearchForm::class,
                ChaptersShowTags::class,
                ChaptersShowBookTree::class,
            ],
            'right' => [
                ChaptersShowDetails::class,
                ChaptersShowActions::class,
            ],
        ],
        'pages-show' => [
            'left' => [
                PagesShowTags::class,
                PagesShowAttachments::class,
                PagesShowPageNav::class,
                PagesShowBookTree::class,
            ],
            'right' => [
                PagesShowDetails::class,
                PagesShowActions::class,
            ],
        ],
    ];

    /**
     * Register a block type to be displayed at the given location and position.
     * @param class-string<ViewBlockInterface> $blockClass
     */
    public function register(string $location, string $position, string $blockClass): void
    {
        if (!isset($this->blocksByLocationAndPosition[$location])) {
            $this->blocksByLocationAndPosition[$location] = [];
        }

        if (!isset($this->blocksByLocationAndPosition[$location][$position])) {
            $this->blocksByLocationAndPosition[$location][$position] = [];
        }

        $this->blocksByLocationAndPosition[$location][$position][] = $blockClass;
    }

    /**
     * Get all blocks registered for a given location and position.
     *
     * @return ViewBlockInterface[]
     * @throws BindingResolutionException
     */
    public function getForLocation(string $location, string $position): array
    {
        $sections = array_unique($this->blocksByLocationAndPosition[$location][$position] ?? []);
        return array_map(function (string $className) {
            return app()->make($className);
        }, $sections);
    }
}
