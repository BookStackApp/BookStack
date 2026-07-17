<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityHtmlDescription;

interface HasDescriptionInterface
{
    public function descriptionInfo(): EntityHtmlDescription;
}
