<?php

namespace Tests\Util;

use BookStack\Util\DateFormatter;
use Carbon\Carbon;
use Tests\TestCase;

class DateFormatterTest extends TestCase
{
    public function test_iso_with_timezone_alters_from_stored_to_display_timezone()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = new Carbon('2020-06-01 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertEquals('2020-06-01 13:00:00 BST', $result);
    }

    public function test_iso_with_timezone_works_from_non_utc_dates()
    {
        $formatter = new DateFormatter('Asia/Shanghai');
        $dateTime = new Carbon('2025-06-10 15:25:00', 'America/New_York');

        $result = $formatter->absolute($dateTime);
        $this->assertEquals('2025-06-11 03:25:00 CST', $result);
    }

    public function test_relative()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subMinutes(50);

        $result = $formatter->relative($dateTime);
        $this->assertEquals('50 minutes ago', $result);
    }
}
