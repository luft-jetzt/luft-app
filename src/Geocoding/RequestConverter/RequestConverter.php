<?php declare(strict_types=1);

namespace App\Geocoding\RequestConverter;

use App\Entity\Zip;
use App\Geocoding\Geocoder\GeocoderInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coordinate\Coordinate;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;
use Doctrine\Persistence\ManagerRegistry;
use Geocoder\Provider\Nominatim\Model\NominatimAddress;
use Geocoder\Provider\Nominatim\Nominatim;
use Symfony\Component\HttpFoundation\Request;

class RequestConverter implements RequestConverterInterface
{
    protected GeocoderInterface $geocoder;

    protected ManagerRegistry $registry;

    public function __construct(GeocoderInterface $geocoder, ManagerRegistry $registry)
    {
        $this->geocoder = $geocoder;
        $this->registry = $registry;
    }

    public function getCoordByRequest(Request $request): ?CoordinateInterface
    {
        $latitude = (float) $request->query->get('latitude');
        $longitude = (float) $request->query->get('longitude');
        $query = $request->query->get('query');
        $zipCode = $request->query->get('zip');

        if (($query && preg_match('/^([0-9]{5,5})$/', $query)) || $zipCode) {
            $cityList = $this->geocoder->queryZip($query ?? $zipCode);

            if (count($cityList) > 0) {
                /** @var NominatimAddress $firstResult */
                $firstResult = $cityList->first();

                return new Coordinate($firstResult->getCoordinates()->getLatitude(), $firstResult->getCoordinates()->getLongitude());
            }
        }

        if ($latitude && $longitude) {
            $coord = new Coordinate(
                $latitude,
                $longitude
            );

            return $coord;
        }

        if ($query) {
            $result = $this->geocoder->query($query);

            $firstResult = array_pop($result);

            if ($firstResult) {
                $coord = new Coordinate($firstResult['value']['latitude'], $firstResult['value']['longitude']);

                return $coord;
            }
        }

        return null;
    }
}
