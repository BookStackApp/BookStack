<?php

namespace BookStack\Sorting;

class BookSortMapItem
{
    public function __construct(
        public int $id,
        public int $sort,
        public int|null $parentChapterId,
        public string $type,
        public int $parentBookId,
    ) {
    }
}
