<?php

namespace BookStack\Util;

use Carbon\Carbon;

class DateFormatter
{
    public function __construct(
        protected string $displayTimezone,
    ) {
    }

    public function isoWithTimezone(Carbon $date): string
    {
        $withDisplayTimezone = $date->clone()->setTimezone($this->displayTimezone);

        return $withDisplayTimezone->format('Y-m-d H:i:s T');
    }

    public function relative(Carbon $date): string
    {
        return $date->diffForHumans();
    }
}
