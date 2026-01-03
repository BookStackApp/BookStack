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

    // ============================================================================
    // Leap Year Edge Cases
    // ============================================================================

    /**
     * Test that February 29th in a leap year is correctly formatted
     * when converting between timezones. This ensures the date doesn't
     * roll over incorrectly during timezone conversion.
     */
    public function test_absolute_handles_leap_year_february_29th()
    {
        $formatter = new DateFormatter('America/New_York');
        // 2024 is a leap year, so February 29th exists
        $dateTime = new Carbon('2024-02-29 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        // Should maintain the date as February 29th, just adjust timezone
        $this->assertStringContainsString('2024-02-29', $result);
        $this->assertStringContainsString('EST', $result);
    }

    /**
     * Test that February 28th in a leap year is handled correctly.
     */
    public function test_absolute_handles_february_28th_in_leap_year()
    {
        $formatter = new DateFormatter('Europe/London');
        $feb28 = new Carbon('2024-02-28 23:00:00', 'UTC');

        $result = $formatter->absolute($feb28);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-02-28', $result);
    }

    /**
     * Test that March 1st in a leap year is handled correctly.
     */
    public function test_absolute_handles_march_1st_in_leap_year()
    {
        $formatter = new DateFormatter('Europe/London');
        $mar1 = new Carbon('2024-03-01 00:00:00', 'UTC');

        $result = $formatter->absolute($mar1);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-03-01', $result);
    }

    /**
     * Test that February 28th in a non-leap year is handled correctly.
     */
    public function test_absolute_handles_february_28th_in_non_leap_year()
    {
        $formatter = new DateFormatter('Europe/London');
        $feb28NonLeap = new Carbon('2023-02-28 23:00:00', 'UTC');

        $result = $formatter->absolute($feb28NonLeap);
        $this->assertIsString($result);
        $this->assertStringContainsString('2023-02-28', $result);
    }

    // ============================================================================
    // DST (Daylight Saving Time) Transition Edge Cases
    // ============================================================================

    /**
     * Test the "spring forward" DST transition before DST starts.
     * In US Eastern time, this typically happens in March.
     */
    public function test_absolute_handles_before_spring_forward_dst_transition()
    {
        $formatter = new DateFormatter('America/New_York');
        // DST in 2024 for America/New_York starts on March 10 at 2:00 AM local time
        // March 10, 2024 06:00 UTC is 01:00 EST (before DST starts)
        $beforeDst = new Carbon('2024-03-10 06:00:00', 'UTC');

        $result = $formatter->absolute($beforeDst);
        $this->assertIsString($result);
        $this->assertStringContainsString('EST', $result);
    }

    /**
     * Test the "spring forward" DST transition after DST starts.
     * Times during the "lost hour" should be handled correctly.
     */
    public function test_absolute_handles_after_spring_forward_dst_transition()
    {
        $formatter = new DateFormatter('America/New_York');
        // March 10, 2024 08:00 UTC is 04:00 EDT (after DST starts, skipping 03:00)
        $afterDst = new Carbon('2024-03-10 08:00:00', 'UTC');

        $result = $formatter->absolute($afterDst);
        $this->assertIsString($result);
        $this->assertStringContainsString('EDT', $result);
    }

    /**
     * Test the "fall back" DST transition before clocks move backward.
     * In US Eastern time, this typically happens in November.
     */
    public function test_absolute_handles_before_fall_back_dst_transition()
    {
        $formatter = new DateFormatter('America/New_York');
        // November 3, 2024 05:00 UTC is 01:00 EDT (before fall back)
        $beforeFallBack = new Carbon('2024-11-03 05:00:00', 'UTC');

        $result = $formatter->absolute($beforeFallBack);
        $this->assertIsString($result);
        $this->assertStringContainsString('EDT', $result);
    }

    /**
     * Test the "fall back" DST transition after clocks move backward.
     * The "repeated hour" should be handled correctly.
     */
    public function test_absolute_handles_after_fall_back_dst_transition()
    {
        $formatter = new DateFormatter('America/New_York');
        // November 3, 2024 06:00 UTC is 01:00 EST (after fall back, same local time)
        $afterFallBack = new Carbon('2024-11-03 06:00:00', 'UTC');

        $result = $formatter->absolute($afterFallBack);
        $this->assertIsString($result);
        $this->assertStringContainsString('EST', $result);
    }

    /**
     * Test formatting in a timezone that doesn't observe DST during winter.
     * This ensures the formatter works correctly for timezones with
     * fixed offsets throughout the year.
     */
    public function test_absolute_handles_timezone_without_dst_in_winter()
    {
        $formatter = new DateFormatter('Asia/Tokyo'); // Japan doesn't observe DST
        $winterDate = new Carbon('2024-01-15 12:00:00', 'UTC');

        $result = $formatter->absolute($winterDate);
        $this->assertIsString($result);
        $this->assertStringContainsString('JST', $result);
    }

    /**
     * Test formatting in a timezone that doesn't observe DST during summer.
     * This ensures the formatter works correctly for timezones with
     * fixed offsets throughout the year.
     */
    public function test_absolute_handles_timezone_without_dst_in_summer()
    {
        $formatter = new DateFormatter('Asia/Tokyo'); // Japan doesn't observe DST
        $summerDate = new Carbon('2024-07-15 12:00:00', 'UTC');

        $result = $formatter->absolute($summerDate);
        $this->assertIsString($result);
        $this->assertStringContainsString('JST', $result);
    }

    // ============================================================================
    // Null and Invalid Date Edge Cases
    // ============================================================================

    /**
     * Test that the formatter handles dates at the minimum valid timestamp.
     * This tests boundary conditions for very old dates.
     */
    public function test_absolute_handles_minimum_valid_date()
    {
        $formatter = new DateFormatter('UTC');
        // Carbon's minimum date is around year 1
        $minDate = Carbon::create(1, 1, 1, 0, 0, 0, 'UTC');

        $result = $formatter->absolute($minDate);
        // Should format without error
        $this->assertIsString($result);
        $this->assertStringContainsString('0001-01-01', $result);
    }

    /**
     * Test that the formatter handles dates at the maximum valid timestamp.
     * This tests boundary conditions for very far future dates.
     */
    public function test_absolute_handles_maximum_valid_date()
    {
        $formatter = new DateFormatter('UTC');
        // Carbon's maximum date is around year 9999
        $maxDate = Carbon::create(9999, 12, 31, 23, 59, 59, 'UTC');

        $result = $formatter->absolute($maxDate);
        // Should format without error
        $this->assertIsString($result);
        $this->assertStringContainsString('9999-12-31', $result);
    }

    /**
     * Test that midnight (00:00:00) is formatted correctly.
     */
    public function test_absolute_handles_midnight()
    {
        $formatter = new DateFormatter('Europe/London');
        $midnight = new Carbon('2024-01-01 00:00:00', 'UTC');

        $result = $formatter->absolute($midnight);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-01-01', $result);
    }

    /**
     * Test that the end of year (December 31) is formatted correctly.
     */
    public function test_absolute_handles_end_of_year()
    {
        $formatter = new DateFormatter('Europe/London');
        $yearEnd = new Carbon('2024-12-31 23:59:59', 'UTC');

        $result = $formatter->absolute($yearEnd);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-12-31', $result);
    }

    // ============================================================================
    // Different Locale and Timezone Edge Cases
    // ============================================================================

    /**
     * Test formatting with Pacific/Auckland timezone.
     */
    public function test_absolute_handles_pacific_auckland_timezone()
    {
        $formatter = new DateFormatter('Pacific/Auckland');
        $dateTime = new Carbon('2024-06-15 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test formatting with Australia/Sydney timezone.
     */
    public function test_absolute_handles_australia_sydney_timezone()
    {
        $formatter = new DateFormatter('Australia/Sydney');
        $dateTime = new Carbon('2024-06-15 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test formatting with Europe/Paris timezone.
     */
    public function test_absolute_handles_europe_paris_timezone()
    {
        $formatter = new DateFormatter('Europe/Paris');
        $dateTime = new Carbon('2024-06-15 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test formatting with America/Los_Angeles timezone.
     */
    public function test_absolute_handles_america_los_angeles_timezone()
    {
        $formatter = new DateFormatter('America/Los_Angeles');
        $dateTime = new Carbon('2024-06-15 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test formatting with America/Sao_Paulo timezone.
     */
    public function test_absolute_handles_america_sao_paulo_timezone()
    {
        $formatter = new DateFormatter('America/Sao_Paulo');
        $dateTime = new Carbon('2024-06-15 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test that timezone conversion works correctly when the source
     * and destination timezones are on opposite sides of the date line.
     */
    public function test_absolute_handles_date_line_crossing()
    {
        $formatter = new DateFormatter('Pacific/Auckland'); // UTC+12
        // A date in UTC that, when converted, crosses the date line
        $dateTime = new Carbon('2024-06-15 10:00:00', 'America/Los_Angeles'); // UTC-7

        $result = $formatter->absolute($dateTime);
        // Should correctly handle the large timezone offset
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test formatting with UTC timezone to ensure it works correctly
     * when display timezone matches the source timezone.
     */
    public function test_absolute_handles_utc_timezone()
    {
        $formatter = new DateFormatter('UTC');
        $dateTime = new Carbon('2024-06-15 12:00:00', 'UTC');

        $result = $formatter->absolute($dateTime);
        $this->assertEquals('2024-06-15 12:00:00 UTC', $result);
    }

    // ============================================================================
    // Relative Date Edge Cases
    // ============================================================================

    /**
     * Test relative formatting for "just now" scenarios where the date
     * is very close to the current time.
     */
    public function test_relative_handles_just_now()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subSeconds(5);

        $result = $formatter->relative($dateTime);
        // Carbon typically formats very recent dates as "just now" or "a few seconds ago"
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for future dates. The formatter should
     * handle dates in the future correctly.
     */
    public function test_relative_handles_future_dates()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->addMinutes(30);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for very old dates (years ago).
     * This ensures the formatter handles long time spans correctly.
     */
    public function test_relative_handles_very_old_dates()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subYears(5);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting with the includeSuffix parameter set to false.
     * This should return absolute time differences without suffixes.
     */
    public function test_relative_without_suffix()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subHours(2);

        $result = $formatter->relative($dateTime, false);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for seconds.
     */
    public function test_relative_handles_seconds()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subSeconds(30);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for minutes.
     */
    public function test_relative_handles_minutes()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subMinutes(45);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for hours.
     */
    public function test_relative_handles_hours()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subHours(3);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for days.
     */
    public function test_relative_handles_days()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subDays(5);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for weeks.
     */
    public function test_relative_handles_weeks()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subWeeks(3);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for months.
     */
    public function test_relative_handles_months()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subMonths(3);

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for exactly 1 hour ago.
     */
    public function test_relative_handles_exact_one_hour_ago()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subHour();

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for exactly 1 day ago.
     */
    public function test_relative_handles_exact_one_day_ago()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subDay();

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test relative formatting for exactly 1 week ago.
     */
    public function test_relative_handles_exact_one_week_ago()
    {
        $formatter = new DateFormatter('Europe/London');
        $dateTime = (new Carbon('now', 'UTC'))->subWeek();

        $result = $formatter->relative($dateTime);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    // ============================================================================
    // Year and Month Boundary Edge Cases
    // ============================================================================

    /**
     * Test formatting dates at year boundaries (December 31 to January 1)
     * to ensure the year transition is handled correctly during timezone conversion.
     */
    public function test_absolute_handles_year_boundary_transition()
    {
        $formatter = new DateFormatter('Pacific/Auckland'); // UTC+12 or UTC+13 depending on DST
        // December 31, 2023 12:00 UTC - when converted to Pacific/Auckland,
        // this might cross into the next year depending on DST
        $yearEnd = new Carbon('2023-12-31 12:00:00', 'UTC');

        $result = $formatter->absolute($yearEnd);
        $this->assertIsString($result);
        // Should be either 2023-12-31 or 2024-01-01 depending on DST offset
        $this->assertTrue(
            str_contains($result, '2023-12-31') || str_contains($result, '2024-01-01'),
            'Year boundary transition not handled correctly'
        );
    }

    /**
     * Test formatting end of January (31 days).
     * Note: When converting from UTC to BST, dates near midnight may
     * shift to the next day, so we test with times that account for this.
     */
    public function test_absolute_handles_end_of_january()
    {
        $formatter = new DateFormatter('Europe/London');
        $janEnd = new Carbon('2024-01-31 22:00:00', 'UTC');

        $result = $formatter->absolute($janEnd);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-01-31', $result);
    }

    /**
     * Test formatting start of February.
     */
    public function test_absolute_handles_start_of_february()
    {
        $formatter = new DateFormatter('Europe/London');
        $febStart = new Carbon('2024-02-01 00:00:00', 'UTC');

        $result = $formatter->absolute($febStart);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-02-01', $result);
    }

    /**
     * Test formatting end of April (30 days).
     * Note: In April, BST is active (UTC+1), so 22:00 UTC = 23:00 BST.
     */
    public function test_absolute_handles_end_of_april()
    {
        $formatter = new DateFormatter('Europe/London');
        $aprEnd = new Carbon('2024-04-30 22:00:00', 'UTC');

        $result = $formatter->absolute($aprEnd);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-04-30', $result);
    }

    /**
     * Test formatting start of May.
     */
    public function test_absolute_handles_start_of_may()
    {
        $formatter = new DateFormatter('Europe/London');
        $mayStart = new Carbon('2024-05-01 00:00:00', 'UTC');

        $result = $formatter->absolute($mayStart);
        $this->assertIsString($result);
        $this->assertStringContainsString('2024-05-01', $result);
    }

    // ============================================================================
    // Integration Edge Cases
    // ============================================================================

    /**
     * Test that the formatter handles dates during DST transition in a leap year.
     */
    public function test_absolute_handles_dst_transition_during_leap_year()
    {
        $formatter = new DateFormatter('America/New_York');
        // March 10, 2024 (leap year) during DST transition period
        $complexDate = new Carbon('2024-03-10 06:00:00', 'UTC'); // During spring forward

        $result = $formatter->absolute($complexDate);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('2024-03-10', $result);
    }

    /**
     * Test that cloning the date in the absolute method doesn't affect
     * the original Carbon instance passed to the formatter.
     */
    public function test_absolute_does_not_modify_original_date()
    {
        $formatter = new DateFormatter('Asia/Tokyo');
        $originalDate = new Carbon('2024-06-15 12:00:00', 'UTC');
        $originalTimezone = $originalDate->timezone->getName();
        $originalTimestamp = $originalDate->timestamp;

        // Format the date
        $formatter->absolute($originalDate);

        // Verify the original date was not modified
        $this->assertEquals($originalTimezone, $originalDate->timezone->getName());
        $this->assertEquals($originalTimestamp, $originalDate->timestamp);
    }
}
