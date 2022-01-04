<?php

namespace BookStack\Entities\Tools;

class BookSortMap
{
    /**
     * @var BookSortMapItem[]
     */
    protected $mapData = [];

    public function addItem(BookSortMapItem $mapItem): void
    {
        $this->mapData[] = $mapItem;
    }

    /**
     * @return BookSortMapItem[]
     */
    public function all(): array
    {
        return $this->mapData;
    }

    public static function fromJson(string $json): self
    {
        $map = new static();
        $mapData = json_decode($json);

        foreach ($mapData as $mapDataItem) {
            $item = new BookSortMapItem(
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