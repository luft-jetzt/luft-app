<?php declare(strict_types=1);

namespace App\Geocoding\Geocoder;

use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Nominatim\Model\NominatimAddress;
use Geocoder\Query\GeocodeQuery;

class Geocoder extends AbstractGeocoder
{
    public function query(string $queryString): array
    {
        $query = GeocodeQuery::create($queryString);

        /** @var AddressCollection $addressCollection */
        $addressCollection = $this->provider->geocodeQuery($query);

        $result = [];

        /** @var NominatimAddress $nominatimAddress */
        foreach ($addressCollection->all() as $nominatimAddress) {
            if (!$nominatimAddress->getCountry() || $nominatimAddress->getCountry()->getName() !== 'Deutschland') {
                continue;
            }

            if (!$nominatimAddress->getCoordinates()) {
                continue;
            }

            $latitude = $nominatimAddress->getCoordinates()->getLatitude();
            $longitude = $nominatimAddress->getCoordinates()->getLongitude();

            $url = $this->router->generate('display', ['latitude' => $latitude, 'longitude' => $longitude]);

            $value = [
                'url' => $url,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            if ($nominatimAddress->getStreetName() && $nominatimAddress->getStreetNumber()) {
                $value['name'] = sprintf('%s %s', $nominatimAddress->getStreetName(), $nominatimAddress->getStreetNumber());
            }

            if ($nominatimAddress->getStreetName() && !$nominatimAddress->getStreetNumber()) {
                $value['name'] = $nominatimAddress->getStreetName();
            }

            if ($nominatimAddress->getLocality()) {
                $value['city'] = $nominatimAddress->getLocality();
            }

            if ($nominatimAddress->getPostalCode()) {
                $value['zipCode'] = $nominatimAddress->getPostalCode();
            }

            if ($nominatimAddress->getOSMType()) {
                $osmType = $nominatimAddress->getOSMType();

                if ($osmType === 'city' || $osmType === 'suburb') {
                    $value['icon'] = 'university';
                } elseif ($osmType === 'building' || $osmType === 'residental') {
                    $value['icon'] = 'road';
                }
            }

            $result[] = [
                'value' => $value
            ];
        }

        return $result;
    }
}
