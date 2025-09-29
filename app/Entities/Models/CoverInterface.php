<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityCover;

interface CoverInterface
{
    public function cover(): EntityCover;
}
