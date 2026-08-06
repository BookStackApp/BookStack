<?php

namespace BookStack\View;

use BookStack\View\SidebarSections\BooksIndexRecents;
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
            ]
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
