<?php declare(strict_types=1);

namespace App\Tests\Air\Analysis\CoronaFireworksAnalysis;

use App\Air\Analysis\CoronaFireworksAnalysis\StartDateTimeCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class StartDateTimeCalculatorTest extends TestCase
{
    public function testCalculateStartDateTimeWithYear(): void
    {
        $result = StartDateTimeCalculator::calculateStartDateTime(2023);

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals(2023, $result->year);
        $this->assertEquals(12, $result->month);
        $this->assertEquals(31, $result->day);
        $this->assertEquals(18, $result->hour);
        $this->assertEquals(0, $result->minute);
    }

    public function testCalculateStartDateTimeTimeIsEvening(): void
    {
        $result = StartDateTimeCalculator::calculateStartDateTime(2024);

        // Should be set to 18:00 (6 PM) on New Year's Eve
        $this->assertEquals('18:00:00', $result->format('H:i:s'));
    }

    public function testCalculateStartDateTimeHasCorrectTimezone(): void
    {
        $result = StartDateTimeCalculator::calculateStartDateTime(2024);

        $this->assertEquals('Europe/Berlin', $result->getTimezone()->getName());
    }

    public function testCalculateStartDateTimeWithoutYearUsesCalculatedYear(): void
    {
        $result = StartDateTimeCalculator::calculateStartDateTime();

        $expectedYear = StartDateTimeCalculator::calculateStartYear();
        $this->assertEquals($expectedYear, $result->year);
    }

    public function testCalculateStartYearReturnsInteger(): void
    {
        $result = StartDateTimeCalculator::calculateStartYear();

        $this->assertIsInt($result);
    }

    public function testCalculateStartYearIsReasonable(): void
    {
        $result = StartDateTimeCalculator::calculateStartYear();
        $currentYear = (int) date('Y');

        // The calculated year should be either current year or last year
        $this->assertGreaterThanOrEqual($currentYear - 1, $result);
        $this->assertLessThanOrEqual($currentYear, $result);
    }

    public function testMultipleYears(): void
    {
        $years = [2019, 2020, 2021, 2022, 2023, 2024];

        foreach ($years as $year) {
            $result = StartDateTimeCalculator::calculateStartDateTime($year);
            $this->assertEquals($year, $result->year);
            $this->assertEquals(12, $result->month);
            $this->assertEquals(31, $result->day);
        }
    }
}
