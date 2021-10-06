<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use Carbon\Carbon;

class StartDateTimeCalculator
{
    private function __construct()
    {

    }

    public static function calculateStartDateTime(int $year = null): Carbon
    {
        if (!$year) {
            $year = static::calculateStartYear();
        }

        $startDateTimeSpec = '%d-12-31 12:00:00';
        return new Carbon(sprintf($startDateTimeSpec, $year));
    }

    public static function calculateStartYear(): int
    {
        return (int) (new Carbon())->subDays(360)->format('Y');
    }
}
