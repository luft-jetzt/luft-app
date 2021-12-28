<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Air\Measurement\MeasurementInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Elastica\Query\BoolQuery;

interface ValueFetcherInterface
{
    public function fetchValues(CoordInterface $coord, int $year, float $maxDistance = 15): array;
}