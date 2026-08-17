<?php

namespace BookStack\View;

use Illuminate\Contracts\Container\BindingResolutionException;

class ViewBlockManager
{
    public function __construct(
        protected ViewBlockPreferences $preferences,
    ) {
    }

    /**
     * @var array<string, array<string, class-string<ViewBlockInterface>[]>>
     */
    protected array $blocksByLocationAndPosition = [];

    /**
     * @var array<string, array<string, class-string<ViewBlockInterface>[]>>
     */
    protected array $locationBlockCache = [];

    /**
     * Register a block type to be displayed at the given location and position.
     * @param class-string<ViewBlockInterface> $blockClass
     */
    public function register(string $location, string $defaultPosition, string $blockClass): void
    {
        if (!isset($this->blocksByLocationAndPosition[$location])) {
            $this->blocksByLocationAndPosition[$location] = [];
        }

        if (!isset($this->blocksByLocationAndPosition[$location][$defaultPosition])) {
            $this->blocksByLocationAndPosition[$location][$defaultPosition] = [];
        }

        if (!is_a($blockClass, ViewBlockInterface::class, true)) {
            throw new \InvalidArgumentException('When registering a view block, the block class must implement ViewBlockInterface');
        }

        $this->blocksByLocationAndPosition[$location][$defaultPosition][] = $blockClass;
    }

    /**
     * Get all blocks registered for a given location and position, considering the
     * preferences for the current user.
     * @return ViewBlockInterface[]
     * @throws BindingResolutionException
     */
    public function getInstancesForLocationAndPositionForCurrentUser(string $location, string $position): array
    {
        $key = $location;
        if (isset($this->locationBlockCache[$key])) {
            $blocks = $this->locationBlockCache[$key][$position] ?? [];
            return $this->blocksToInstances($blocks);
        }

        $forLocation = $this->getForLocationForCurrentUser($location);
        $this->locationBlockCache[$key] = $forLocation;

        $blocks = $forLocation[$position] ?? [];
        return $this->blocksToInstances($blocks);
    }

    /**
     * Create instances of the given block classes.
     * @param class-string<ViewBlockInterface>[] $blocks
     * @return ViewBlockInterface[]
     * @throws BindingResolutionException
     */
    protected function blocksToInstances(array $blocks): array
    {
        return array_map(fn (string $blockClass) => app()->make($blockClass), $blocks);
    }

    /**
     * Get all blocks registered for a given location, as sets of arrays
     * keyed by position.
     * @return array<string, class-string<ViewBlockInterface>[]>
     */
    protected function getForLocation(string $location): array
    {
        $defaults = ViewBlockDefaults::getForLocation($location) ?? [];
        $registered = $this->blocksByLocationAndPosition[$location] ?? [];
        return array_merge_recursive($defaults, $registered);
    }

    /**
     * Get all blocks registered for a given location, as sets of arrays
     * keyed by position, for the current user.
     * Same as above but with user-specific preferences applied.
     * @return array<string, class-string<ViewBlockInterface>[]>
     * @throws BindingResolutionException
     */
    public function getForLocationForCurrentUser(string $location): array
    {
        $forLocation = $this->getForLocation($location);
        $userBlocksByPosition = $this->preferences->getIdByPositionMap($location);
        if (empty($userBlocksByPosition)) {
            return $forLocation;
        }

        $results = [];
        $blocksById = $this->blocksByPositionToIdMap($forLocation);
        $idPositionMap = $this->blocksByPositionToIdPositionMap($forLocation);
        $locations = array_keys($forLocation);
        $locations[] = 'unused';

        // Add based on user preferences
        foreach ($locations as $position) {
            $userBlockIds = $userBlocksByPosition[$position] ?? [];
            $results[$position] = [];
            foreach ($userBlockIds as $blockId) {
                $block = $blocksById[$blockId] ?? null;
                if ($block && isset($blocksById[$blockId])) {
                    $results[$position][] = $block;
                    unset($blocksById[$blockId]);
                }
            }
        }

        // Add remaining blocks based on their default locations
        foreach ($blocksById as $block) {
            $position = $idPositionMap[$block::getId()] ?? 'unused';
            $results[$position][] = $block;
        }

        return $results;
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

        $usingDefaultHome = setting('app-homepage-type') === 'default';
        $toIgnore = $usingDefaultHome ? 'home-non-default' : 'home-default';
        unset($results[$toIgnore]);

        return $results;
    }


    /**
     * Update user preferences for a given location to match the given layout data map.
     * @param array<string, string[]> $layoutData
     * @throws BindingResolutionException
     */
    public function updatePreferencesFromIdPositionMap(string $location, array $layoutData): void
    {
        $this->preferences->storeByIdPositionMap(
            $location,
            $layoutData,
            $this->getForLocation($location),
        );
    }

    /**
     * Clear the local user-specific cache of blocks.
     * The cache only needs to exist for the current request time since its purpose is to
     * avoid duplicate loading across views.
     */
    public function clearLocalCache(): void
    {
        $this->locationBlockCache = [];
    }

    /**
     * Convert a blocksByPosition array into a map of block IDs to blocks.
     * @param array<string, class-string<ViewBlockInterface>[]> $blocksByPosition
     * @return array<string, ViewBlockInterface>
     */
    protected function blocksByPositionToIdMap(array $blocksByPosition): array
    {
        $map = [];
        foreach ($blocksByPosition as $position => $blocks) {
            foreach ($blocks as $block) {
                $map[$block::getId()] = $block;
            }
        }
        return $map;
    }

    /**
     * Convert a blocksByPosition array into a map of block IDs to their positions.
     * @param array<string, class-string<ViewBlockInterface>[]> $blocksByPosition
     * @return array<string, string>
     */
    protected function blocksByPositionToIdPositionMap(array $blocksByPosition): array
    {
        $map = [];
        foreach ($blocksByPosition as $position => $blocks) {
            foreach ($blocks as $block) {
                $map[$block::getId()] = $position;
            }
        }
        return $map;
    }
}
