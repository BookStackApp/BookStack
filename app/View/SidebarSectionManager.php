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
use Illuminate\Contracts\Container\BindingResolutionException;

class SidebarSectionManager
{
    /**
     * @var array<string, array<string, class-string<SidebarSectionInterface>[]>>
     */
    protected array $sectionByLocation = [
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
