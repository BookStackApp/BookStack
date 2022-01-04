<?php

namespace BookStack\Entities\Tools;

class BookSortMapItem
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $sort;

    /**
     * @var ?int
     */
    public $parentChapterId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $parentBookId;


    public function __construct(int $id, int $sort, ?int $parentChapterId, string $type, int $parentBookId)
    {
        $this->id = $id;
        $this->sort = $sort;
        $this->parentChapterId = $parentChapterId;
        $this->type = $type;
        $this->parentBookId = $parentBookId;
    }


}