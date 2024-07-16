<?php declare(strict_types=1);

namespace App\Air\Analysis\CoronaFireworksAnalysis;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;

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

        $startDateTimeSpec = '%d-12-31 18:00:00';
        return new Carbon(sprintf($startDateTimeSpec, $year), new CarbonTimeZone('Europe/Berlin'));
    }

    public static function calculateStartYear(): int
    {
        return (int) (new Carbon())->subDays(350)->format('Y');
    }
}
