<?php declare(strict_types=1);

namespace App\Pollution\StationFinder;

use Caldera\GeoBasic\Coord\CoordInterface;

interface StationFinderInterface
{
    public function setCoord(CoordInterface $coord): StationFinderInterface;
    public function findNearestStations(float $maxDistance = 20.0): array;
}
