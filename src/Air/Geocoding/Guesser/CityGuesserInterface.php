<?php declare(strict_types=1);

namespace App\Air\Geocoding\Guesser;

use App\Geo\Coord\CoordInterface;

interface CityGuesserInterface
{
    public function guessForLatLng(float $latitude, float $longitude): ?string;
    public function guess(CoordInterface $coord): ?string;
}
