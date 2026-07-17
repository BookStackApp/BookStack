<?php

namespace BookStack\Util;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateFormatter
{
    public function __construct(
        protected string $displayTimezone,
    ) {
    }

    public function absolute(Carbon $date): string
    {
        $withDisplayTimezone = $date->clone()->setTimezone($this->displayTimezone);

        return $withDisplayTimezone->format('Y-m-d H:i:s T');
    }

    public function relative(Carbon $date, bool $includeSuffix = true): string
    {
        return $date->diffForHumans(null, $includeSuffix ? null : CarbonInterface::DIFF_ABSOLUTE);
    }
}
