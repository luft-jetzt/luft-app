<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use Caldera\GeoBasic\Coord\CoordInterface;

interface ValueFetcherInterface
{
    public function fetchValues(CoordInterface $coord, float $maxDistance = 15): array;
}
