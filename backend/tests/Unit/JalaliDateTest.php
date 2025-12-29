<?php

namespace Tests\Unit;

use App\ValueObjects\JalaliDate;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class JalaliDateTest extends TestCase
{
    /** @test */
    public function it_can_create_from_gregorian()
    {
        // 2024-03-20 is 1403-01-01

        $gregorian = Carbon::create(2024, 3, 20);
        $jalali = JalaliDate::fromGregorian($gregorian);

        $this->assertEquals(1403, $jalali->getYear());
        $this->assertEquals(1, $jalali->getMonth());
        $this->assertEquals(1, $jalali->getDay());
    }

    /** @test */
    public function it_can_convert_to_gregorian()
    {
        $jalali = new JalaliDate(1403, 1, 1);
        $gregorian = $jalali->toGregorian();

        $this->assertEquals(2024, $gregorian->year);
        $this->assertEquals(3, $gregorian->month);
        $this->assertEquals(20, $gregorian->day);
    }

    /** @test */
    public function it_can_parse_jalali_string()
    {
        $jalali = JalaliDate::parse('1403/10/09');

        $this->assertEquals(1403, $jalali->getYear());
        $this->assertEquals(10, $jalali->getMonth());
        $this->assertEquals(9, $jalali->getDay());
    }

    /** @test */
    public function it_can_format_date()
    {
        $jalali = new JalaliDate(1403, 1, 1);

        $this->assertEquals('1403/01/01', $jalali->format('Y/m/d', false));
        $this->assertEquals('۱۴۰۳/۰۱/۰۱', $jalali->format('Y/m/d', true));
        $this->assertEquals('1 فروردین 1403', $jalali->format('j F Y', false));
    }

    /** @test */
    public function it_can_check_leap_year()
    {
        // 1403 is a leap year in Jalali
        $this->assertTrue(JalaliDate::isLeapJalaliYear(1403));
        $this->assertFalse(JalaliDate::isLeapJalaliYear(1402));
    }

    /** @test */
    public function it_can_get_days_in_month()
    {
        $this->assertEquals(31, JalaliDate::getDaysInMonth(1403, 1)); // Farvardin
        $this->assertEquals(30, JalaliDate::getDaysInMonth(1403, 7)); // Mehr
        $this->assertEquals(30, JalaliDate::getDaysInMonth(1403, 12)); // Esfand (Leap)
        $this->assertEquals(29, JalaliDate::getDaysInMonth(1402, 12)); // Esfand (Normal)
    }

    /** @test */
    public function it_can_compare_dates()
    {
        $date1 = new JalaliDate(1403, 1, 1);
        $date2 = new JalaliDate(1403, 1, 2);

        $this->assertTrue($date1->lt($date2));
        $this->assertFalse($date1->gt($date2));
        $this->assertTrue($date2->eq($date2));
    }

    /** @test */
    public function it_can_add_days()
    {
        $date = new JalaliDate(1402, 12, 29);
        $newDate = $date->addDays(1); // Should be 1403-01-01 if 1402 wasn't leap, but 1403 is leap. Wait, 1403 is leap. 1402 is not.
        // 1402 (2023-2024) - Is 1402 leap?
        // Using library logic.

        // Let's rely on calculation
        // 1402-12-29 + 1 day -> 1403-01-01

        $this->assertEquals(1, $newDate->getDay());
        $this->assertEquals(1, $newDate->getMonth());
        $this->assertEquals(1403, $newDate->getYear());
    }
}
