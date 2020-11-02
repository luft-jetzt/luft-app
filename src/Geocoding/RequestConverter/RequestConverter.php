<?php declare(strict_types=1);

namespace App\Geocoding\RequestConverter;

use App\Entity\Zip;
use App\Geocoding\Geocoder\GeocoderInterface;
use Caldera\GeoBasic\Coord\Coord;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestConverter implements RequestConverterInterface
{
    protected GeocoderInterface $geocoder;

    protected RegistryInterface $registry;

    public function __construct(GeocoderInterface $geocoder, RegistryInterface $registry)
    {
        $this->geocoder = $geocoder;
        $this->registry = $registry;
    }

    public function getCoordByRequest(Request $request): ?Coord
    {
        $latitude = (float) $request->query->get('latitude');
        $longitude = (float) $request->query->get('longitude');
        $query = $request->query->get('query');
        $zipCode = $request->query->get('zip');

        if (($query && preg_match('/^([0-9]{5,5})$/', $query)) || $zipCode) {
            $zip = $this->registry->getRepository(Zip::class)->findOneByZip($zipCode ?? $query);

            return $zip;
        }

        if ($latitude && $longitude) {
            $coord = new Coord(
                $latitude,
                $longitude
            );

            return $coord;
        }

        if ($query) {
            $result = $this->geocoder->query($query);

            $firstResult = array_pop($result);

            if ($firstResult) {
                $coord = new Coord($firstResult['value']['latitude'], $firstResult['value']['longitude']);

                return $coord;
            }
        }

        return null;
    }
}
