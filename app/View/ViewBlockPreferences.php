<?php

namespace BookStack\View;

use Illuminate\Contracts\Container\BindingResolutionException;

class ViewBlockPreferences
{
    /**
     * From the request data of the layout editor, validate the request data to ensure the blocks
     * and locations are valid and then store the layout data.
     * The request data is expected to be in the format of:
     * [
     *     'position-1' => ['block-id-1', 'block-id-2'],
     *     'position-2' => ['block-id-3'],
     * ]
     * @param array<string, string[]> $layoutData
     * @param array<string, ViewBlockInterface[]> $validBlocksByPosition
     * @throws BindingResolutionException
     */
    public function storeByIdPositionMap(
        string $location,
        array $layoutData,
        array $validBlocksByPosition,
    ): void {
        $validIds = $this->extractValidBlockIds($validBlocksByPosition);
        $validPositions = array_keys($validBlocksByPosition);
        $validPositions[] = 'unused';

        // Ignore updates for invalid/unknown locations
        if (empty($validBlocksByPosition)) {
            return;
        }

        /** @var array<string, string[]> $validatedLayoutData */
        $validatedLayoutData = [];

        foreach ($layoutData as $position => $blockIds) {
            if (!in_array($position, $validPositions)) {
                continue;
            }

            $validatedLayoutData[$position] = array_intersect($blockIds, $validIds);
        }

        $settingKey = $this->getSettingKey($location);
        setting()->putForCurrentUser($settingKey, json_encode($validatedLayoutData));
    }

    /**
     * Get the layou data for a given location.
     * Provides arrays of block ids keyed by position.
     * @return array<string, string[]>
     */
    public function getIdByPositionMap(string $location): array
    {
        $settingKey = $this->getSettingKey($location);
        $layoutData = setting()->getForCurrentUser($settingKey, '{}');
        return json_decode($layoutData, true) ?? [];
    }

    protected function getSettingKey(string $location): string
    {
        return 'view-layout#' . $location;
    }

    /**
     * @param array<string, ViewBlockInterface[]> $blocksByPosition
     * @return string[]
     */
    protected function extractValidBlockIds(array $blocksByPosition): array
    {
        $ids = [];

        foreach ($blocksByPosition as $blocks) {
            foreach ($blocks as $block) {
                $ids[] = $block->getId();
            }
        }

        return array_unique($ids);
    }
}
