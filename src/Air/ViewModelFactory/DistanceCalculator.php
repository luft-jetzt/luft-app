<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Entity\Station;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

class DistanceCalculator
{
    private function __construct()
    {

    }

    public static function distance(CoordinateInterface $coord, Station $station): ?float
    {
        $geotools = new \League\Geotools\Geotools();

        $coordA = new \League\Geotools\Coordinate\Coordinate($coord->toArray());
        $coordB = new \League\Geotools\Coordinate\Coordinate($station->toArray());

        $distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);

        return round($distance->in('km')->haversine(), 2);
    }
}