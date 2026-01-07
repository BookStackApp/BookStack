<?php

namespace BookStack\Users\Models;

interface OwnableInterface
{
    public function getOwnerFieldName(): string;
}
