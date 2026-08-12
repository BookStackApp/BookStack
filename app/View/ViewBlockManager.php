<?php

namespace BookStack\View;

use Illuminate\Contracts\Container\BindingResolutionException;

class ViewBlockManager
{
    /**
     * @var array<string, array<string, class-string<ViewBlockInterface>[]>>
     */
    protected array $blocksByLocationAndPosition = [];

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
    public function getForLocationAndPosition(string $location, string $position): array
    {
        $defaults = ViewBlockDefaults::getForLocation($location)[$position] ?? [];
        $registered = $this->blocksByLocationAndPosition[$location][$position] ?? [];
        $sections = array_unique(array_merge($defaults, $registered));

        return array_map(function (string $className) {
            return app()->make($className);
        }, $sections);
    }

    /**
     * Get all blocks registered for a given location, as sets of arrays
     * keyed by position.
     * @return array<string, ViewBlockInterface[]>
     * @throws BindingResolutionException
     */
    public function getForLocation(string $location): array
    {
        $defaults = ViewBlockDefaults::getForLocation($location) ?? [];
        $registered = $this->blocksByLocationAndPosition[$location] ?? [];
        $sections = array_merge_recursive($defaults, $registered);

        $instances = [];
        foreach ($sections as $position => $blocks) {
            $instances[$position] = array_map(function (string $className) {
                return app()->make($className);
            }, $blocks);
        }

        return $instances;
    }

    /**
     * Get the names of all locations where blocks are registered.
     * Returns an array where the keys are location strings, and the
     * values are translated labels for that location.
     * @return array<string, string>
     */
    public function getNamedLocations(): array
    {
        $labels = ViewBlockDefaults::getLocationLabels();
        $defaults = ViewBlockDefaults::getLocations();
        $registered = array_keys($this->blocksByLocationAndPosition);
        $merged = array_unique(array_merge($defaults, $registered));

        $results = [];
        foreach ($merged as $location) {
            $results[$location] = $labels[$location] ?? $location;
        }

        return $results;
    }
}
