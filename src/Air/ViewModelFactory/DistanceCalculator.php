<?php declare(strict_types=1);

namespace App\Air\ViewModelFactory;

use App\Entity\Station;
use App\Geo\Coord\CoordInterface;

class DistanceCalculator
{
    /** WGS84 semi-major axis in meters. */
    private const WGS84_A = 6378137.0;

    /** WGS84 inverse flattening. */
    private const WGS84_INV_F = 298.257223563;

    private function __construct()
    {
    }

    public static function distance(CoordInterface $coord, Station $station): ?float
    {
        $latA = deg2rad($coord->getLatitude());
        $lngA = deg2rad($coord->getLongitude());
        $latB = deg2rad($station->getLatitude());
        $lngB = deg2rad($station->getLongitude());

        $dLat = $latB - $latA;
        $dLng = $lngB - $lngA;

        $a = sin($dLat / 2) ** 2 + cos($latA) * cos($latB) * sin($dLng / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        $meanRadius = self::WGS84_A * (1 - 1 / self::WGS84_INV_F / 3);

        return round($meanRadius * $c / 1000, 2);
    }
}