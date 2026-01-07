<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityDefaultTemplate;

interface HasDefaultTemplateInterface
{
    public function defaultTemplate(): EntityDefaultTemplate;
}
