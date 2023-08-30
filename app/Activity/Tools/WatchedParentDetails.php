<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\WatchLevels;

class WatchedParentDetails
{
    public function __construct(
        public string $type,
        public int $level,
    ) {
    }

    public function ignoring(): bool
    {
        return $this->level === WatchLevels::IGNORE;
    }
}
