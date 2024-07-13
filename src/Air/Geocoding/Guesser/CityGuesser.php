<?php declare(strict_types=1);

namespace App\Air\Geocoding\Guesser;

use Caldera\GeoBasic\Coord\Coord;
use Geocoder\Exception\InvalidServerResponse;
use Geocoder\Model\AddressCollection;
use Geocoder\Query\ReverseQuery;
use Http\Client\Exception\NetworkException;

class CityGuesser extends AbstractGuesser implements CityGuesserInterface
{
    #[\Override]
    public function guessForLatLng(float $latitude, float $longitude): ?string
    {
        $coord = new Coord($latitude, $longitude);

        return $this->guess($coord);
    }

    #[\Override]
    public function guess(Coord $coord): ?string
    {
        $query = ReverseQuery::fromCoordinates($coord->getLatitude(), $coord->getLongitude());

        try {
            /** @var AddressCollection $addressCollection */
            $addressCollection = $this->provider->reverseQuery($query);
        } catch (NetworkException|InvalidServerResponse) {
            return null;
        }

        if (!$addressCollection || !$addressCollection->first() || (!$addressCollection->first()->getLocality() && !$addressCollection->first()->getSubLocality())) {
            return null;
        }

        return $addressCollection->first()->getLocality() ?? $addressCollection->first()->getSubLocality();
    }
}
