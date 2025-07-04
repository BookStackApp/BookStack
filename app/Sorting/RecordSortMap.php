<?php

namespace BookStack\Sorting;

class RecordSortMap
{
    /**
     * @var RecordSortMapItem[]
     */
    protected $mapData = [];

    public function addItem(RecordSortMapItem $mapItem): void
    {
        $this->mapData[] = $mapItem;
    }

    /**
     * @return RecordSortMapItem[]
     */
    public function all(): array
    {
        return $this->mapData;
    }

    public static function fromJson(string $json): self
    {
        $map = new RecordSortMap();
        $mapData = json_decode($json);

        foreach ($mapData as $mapDataItem) {
            $item = new RecordSortMapItem(
                intval($mapDataItem->id),
                intval($mapDataItem->sort),
                $mapDataItem->parentChapter ? intval($mapDataItem->parentChapter) : null,
                $mapDataItem->type,
                intval($mapDataItem->book)
            );

            $map->addItem($item);
        }

        return $map;
    }
}
