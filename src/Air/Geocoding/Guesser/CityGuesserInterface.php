<?php declare(strict_types=1);

namespace App\Air\Geocoding\Guesser;

use App\Geo\Coord\Coord;

interface CityGuesserInterface
{
    public function guessForLatLng(float $latitude, float $longitude): ?string;
    public function guess(Coord $coord): ?string;
}
