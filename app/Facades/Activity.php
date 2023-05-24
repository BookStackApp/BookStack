<?php

namespace BookStack\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \BookStack\Activity\Tools\ActivityLogger
 */
class Activity extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'activity';
    }
}
