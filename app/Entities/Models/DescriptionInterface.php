<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityHtmlDescription;

interface DescriptionInterface
{
    public function description(): EntityHtmlDescription;
}
