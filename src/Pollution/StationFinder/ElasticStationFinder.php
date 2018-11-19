<?php declare(strict_types=1);

namespace App\Pollution\StationFinder;

use App\Provider\ProviderInterface;
use Caldera\GeoBasic\Bounds\BoundsInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use FOS\ElasticaBundle\Finder\FinderInterface;

class ElasticStationFinder implements StationFinderInterface
{
    /**
     * @var CoordInterface $coord
     */
    protected $coord;

    /**
     * @var FinderInterface $stationFinder
     */
    protected $stationFinder;

    public function __construct(FinderInterface $stationFinder)
    {
        $this->stationFinder = $stationFinder;
    }

    public function setCoord(CoordInterface $coord): StationFinderInterface
    {
        $this->coord = $coord;

        return $this;
    }

    public function findNearestStations(float $maxDistance = 20.0, int $size = 100): array
    {
        $matchAll = new \Elastica\Query\MatchAll();

        $geoQuery = new \Elastica\Query\GeoDistance('pin', [
            'lat' => $this->coord->getLatitude(),
            'lon' => $this->coord->getLongitude(),
        ],
        sprintf('%fkm', $maxDistance));

        $untilQuery = new \Elastica\Query\Exists('untilDate');

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($matchAll)
            ->addMustNot($untilQuery)
            ->addFilter($geoQuery);

        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);

        $query->setSize($size);
        $query->setSort([
            '_geo_distance' => [
                'pin' => [
                    'lat' => $this->coord->getLatitude(),
                    'lon' => $this->coord->getLongitude()
                ],
                'order' => 'asc',
                'unit' => 'km',
            ]
        ]);

        $results = $this->stationFinder->find($query);

        return $results;
    }

    public function findStationsInBounds(BoundsInterface $bounds, int $size = null, ProviderInterface $provider = null, array $ignoreIds = []): array
    {
        $matchAll = new \Elastica\Query\MatchAll();

        $geoQuery = new \Elastica\Query\GeoBoundingBox('pin', $bounds->toLatLonArray());

        $untilQuery = new \Elastica\Query\Exists('untilDate');

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($matchAll)
            ->addMustNot($untilQuery)
            ->addFilter($geoQuery);

        if ($provider) {
            $providerQuery = new \Elastica\Query\Term(['provider' => $provider->getIdentifier()]);

            $boolQuery->addMust($providerQuery);
        }

        if (0 !== count($ignoreIds)) {
            $boolQuery->addMustNot(new \Elastica\Query\Terms('_id', $ignoreIds));
        }

        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);

        if ($size) {
            $query->setSize($size);
        } else {
            $query->setSize(5000);
        }

        //var_dump(json_encode($query->toArray()));die;
        $results = $this->stationFinder->find($query);

        return $results;
    }
}
