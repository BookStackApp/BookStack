<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityCover;

interface HasCoverInterface
{
    public function cover(): EntityCover;
}
