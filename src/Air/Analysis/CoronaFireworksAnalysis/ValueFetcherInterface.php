<?php declare(strict_types=1);

namespace App\Air\Analysis\CoronaFireworksAnalysis;

use Caldera\GeoBasic\Coord\CoordInterface;

interface ValueFetcherInterface
{
    public function fetchValues(CoordInterface $coord, array $yearList = [], int $startHour = 12, int $rangeInMinutes = 1440, float $maxDistance = 15): array;
}
