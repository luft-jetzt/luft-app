<?php declare(strict_types=1);

namespace App\Geocoding\Guesser;

use Caldera\GeoBasic\Coord\Coord;
use Geocoder\Model\AddressCollection;
use Geocoder\Query\ReverseQuery;

class CityGuesser extends AbstractGuesser implements CityGuesserInterface
{
    public function guessForLatLng(float $latitude, float $longitude): ?string
    {
        $coord = new Coord($latitude, $longitude);

        return $this->guess($coord);
    }

    public function guess(Coord $coord): ?string
    {
        $query = ReverseQuery::fromCoordinates($coord->getLatitude(), $coord->getLongitude());

        /** @var AddressCollection $addressCollection */
        $addressCollection = $this->provider->reverseQuery($query);

        if (!$addressCollection || !$addressCollection->first() || (!$addressCollection->first()->getLocality() && !$addressCollection->first()->getSubLocality())) {
            return null;
        }

        return $addressCollection->first()->getLocality() ?? $addressCollection->first()->getSubLocality();
    }
}
