<?php

namespace AppBundle\Pollution\StationFinder;

use Caldera\GeoBasic\Coord\CoordInterface;

interface StationFinderInterface
{
    public function setCoord(CoordInterface $coord): StationFinderInterface;
    public function findNearestStations(float $maxDistance = 20.0): array;
}