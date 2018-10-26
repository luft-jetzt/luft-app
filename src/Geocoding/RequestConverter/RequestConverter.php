<?php declare(strict_types=1);

namespace App\Geocoding\RequestConverter;

use App\Entity\Zip;
use App\Geocoding\Query\GeoQueryInterface;
use Caldera\GeoBasic\Coord\Coord;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestConverter implements RequestConverterInterface
{
    /** @var GeoQueryInterface $geoQuery */
    protected $geoQuery;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(GeoQueryInterface $geoQuery, RegistryInterface $registry)
    {
        $this->geoQuery = $geoQuery;
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
            $result = $this->geoQuery->query($query);

            $firstResult = array_pop($result);

            if ($firstResult) {
                $coord = new Coord($firstResult['value']['latitude'], $firstResult['value']['longitude']);

                return $coord;
            }
        }

        return null;
    }
}
