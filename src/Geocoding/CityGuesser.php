<?php declare(strict_types=1);

namespace App\Geocoding;

use Caldera\GeoBasic\Coord\Coord;
use Geocoder\Query\ReverseQuery;

class CityGuesser extends AbstractGeocoding implements CityGuesserInterface
{
    public function guessForLatLng(float $latitude, float $longitude): ?string
    {
        $coord = new Coord($latitude, $longitude);

        return $this->guess($coord);
    }

    public function guess(Coord $coord): ?string
    {
        $result = $this->geocoder->reverseQuery(ReverseQuery::fromCoordinates($coord->getLatitude(), $coord->getLongitude()));

        if (!$result || !$result->first() || (!$result->first()->getLocality() && !$result->first()->getSubLocality())) {
            return null;
        }

        return $result->first()->getLocality() ?? $result->first()->getSubLocality();
    }
}
