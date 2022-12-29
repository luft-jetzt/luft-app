<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Pollution\DataFinder\ElasticFinder;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Elastica\Aggregation\DateHistogram;

class ValueFetcher implements ValueFetcherInterface
{
    public function __construct()
    {
    }

    public function fetchValues(CoordInterface $coord, array $yearList = [], int $startHour = 12, int $rangeInMinutes = 1440, float $maxDistance = 15): array
    {
        return [];

        return $result;
    }
}
