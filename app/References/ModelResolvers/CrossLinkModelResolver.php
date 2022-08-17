<?php

namespace BookStack\References\ModelResolvers;

use BookStack\Model;

interface CrossLinkModelResolver
{
    /**
     * Resolve the given href link value to a model.
     */
    public function resolve(string $link): ?Model;
}