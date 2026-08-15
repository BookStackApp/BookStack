<?php

namespace BookStack\View;

class ViewBlockPreferences
{
    public function __construct(
        protected ViewBlockManager $blockManager
    ) {
    }

    /**
     * From the request data of the layout editor, validate the request data to ensure the blocks
     * and locations are valid and then store the layout data.
     * The request data is expected to be in the format of:
     * [
     *     'position-1' => ['block-id-1', 'block-id-2'],
     *     'position-2' => ['block-id-3'],
     * ]
     * @param array<string, string[]> $layoutData
     */
    public function storeFromLayoutRequestData(string $location, array $layoutData): void
    {
        $validBlocks = $this->blockManager->getForLocation($location);
        $validIds = $this->extractValidBlockIds($validBlocks);
        $validPositions = array_keys($validBlocks);
        $validPositions[] = 'unused';

        // Ignore updates for invalid/unknown locations
        if (empty($validBlocks)) {
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
