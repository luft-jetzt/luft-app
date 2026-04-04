<?php declare(strict_types=1);

namespace App\Air\Geocoding\RequestConverter;

use App\Air\Geocoding\Geocoder\GeocoderInterface;
use App\Entity\Zip;
use App\Geo\Coordinate\Coordinate;
use App\Geo\Coordinate\CoordinateInterface;
use Doctrine\Persistence\ManagerRegistry;
use Geocoder\Exception\QuotaExceeded;
use Geocoder\Provider\Nominatim\Model\NominatimAddress;
use Symfony\Component\HttpFoundation\Request;

class RequestConverter implements RequestConverterInterface
{
    public function __construct(protected GeocoderInterface $geocoder, protected ManagerRegistry $registry)
    {
    }

    #[\Override]
    public function getCoordByRequest(Request $request): ?CoordinateInterface
    {
        $latitude = (float) $request->query->get('latitude');
        $longitude = (float) $request->query->get('longitude');
        $query = $request->query->get('query');
        $zipCode = $request->query->get('zip');

        try {
            if (($query && preg_match('/^([0-9]{5,5})$/', $query)) || $zipCode) {
                $cityList = $this->geocoder->queryZip($query ?? $zipCode);

                if (count($cityList) > 0) {
                    /** @var NominatimAddress $firstResult */
                    $firstResult = $cityList->first();

                    return new Coordinate($firstResult->getCoordinates()->getLatitude(), $firstResult->getCoordinates()->getLongitude());
                }
            }
        } catch (QuotaExceeded) {
            return null;
        }

        if ($latitude && $longitude) {
            $coord = new Coordinate(
                $latitude,
                $longitude
            );

            return $coord;
        }

        try {
            if ($query) {
                $result = $this->geocoder->query($query);

                $firstResult = array_pop($result);

                if ($firstResult) {
                    $coord = new Coordinate($firstResult['value']['latitude'], $firstResult['value']['longitude']);

                    return $coord;
                }
            }
        } catch (QuotaExceeded) {
            return null;
        }

        return null;
    }
}
