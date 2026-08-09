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
    public function getForLocation(string $location, string $position): array
    {
        $defaults = ViewBlockDefaults::getForLocation($location)[$position] ?? [];
        $registered = $this->blocksByLocationAndPosition[$location][$position] ?? [];
        $sections = array_unique(array_merge($defaults, $registered));

        return array_map(function (string $className) {
            return app()->make($className);
        }, $sections);
    }
}
