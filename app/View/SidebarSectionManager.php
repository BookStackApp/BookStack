<?php

namespace BookStack\View;

use BookStack\View\SidebarSections\BooksIndexActions;
use BookStack\View\SidebarSections\BooksIndexNew;
use BookStack\View\SidebarSections\BooksIndexPopular;
use BookStack\View\SidebarSections\BooksIndexRecents;
use BookStack\View\SidebarSections\BooksShowActions;
use BookStack\View\SidebarSections\BooksShowActivity;
use BookStack\View\SidebarSections\BooksShowDetails;
use BookStack\View\SidebarSections\BooksShowSearchForm;
use BookStack\View\SidebarSections\BooksShowShelves;
use BookStack\View\SidebarSections\BooksShowTags;
use BookStack\View\SidebarSections\ChaptersShowActions;
use BookStack\View\SidebarSections\ChaptersShowBookTree;
use BookStack\View\SidebarSections\ChaptersShowDetails;
use BookStack\View\SidebarSections\ChaptersShowSearchForm;
use BookStack\View\SidebarSections\ChaptersShowTags;
use BookStack\View\SidebarSections\PagesShowActions;
use BookStack\View\SidebarSections\PagesShowAttachments;
use BookStack\View\SidebarSections\PagesShowBookTree;
use BookStack\View\SidebarSections\PagesShowDetails;
use BookStack\View\SidebarSections\PagesShowPageNav;
use BookStack\View\SidebarSections\PagesShowTags;
use BookStack\View\SidebarSections\ShelvesIndexActions;
use BookStack\View\SidebarSections\ShelvesIndexNew;
use BookStack\View\SidebarSections\ShelvesIndexPopular;
use BookStack\View\SidebarSections\ShelvesIndexRecents;
use BookStack\View\SidebarSections\ShelvesShowActions;
use BookStack\View\SidebarSections\ShelvesShowActivity;
use BookStack\View\SidebarSections\ShelvesShowDetails;
use BookStack\View\SidebarSections\ShelvesShowTags;
use Illuminate\Contracts\Container\BindingResolutionException;

class SidebarSectionManager
{
    /**
     * @var array<string, array<string, class-string<SidebarSectionInterface>[]>>
     */
    protected array $sectionByLocation = [
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
     * Register a sidebar section to be displayed at the given location and position.
     * @param class-string<SidebarSectionInterface> $sectionClass
     */
    public function register(string $location, string $position, string $sectionClass): void
    {
        if (!isset($this->sectionByLocation[$location])) {
            $this->sectionByLocation[$location] = [];
        }

        if (!isset($this->sectionByLocation[$location][$position])) {
            $this->sectionByLocation[$location][$position] = [];
        }

        $this->sectionByLocation[$location][$position][] = $sectionClass;
    }

    /**
     * Get all sidebar sections registered for a given location.
     *
     * @return SidebarSectionInterface[]
     * @throws BindingResolutionException
     */
    public function getSectionsForLocation(string $location, string $position): array
    {
        $sections = array_unique($this->sectionByLocation[$location][$position] ?? []);
        return array_map(function (string $className) {
            return app()->make($className);
        }, $sections);
    }
}
