<?php declare(strict_types=1);

namespace App\Pollution\StationFinder;

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

    public function findNearestStations(float $maxDistance = 20.0): array
    {
        $matchAll = new \Elastica\Query\MatchAll();
        $geoQuery = new \Elastica\Query\GeoDistance('pin',
            [
                'lat' => $this->coord->getLatitude(),
                'lon' => $this->coord->getLongitude()
            ],
            '20km'
        );

        $untilQuery = new \Elastica\Query\Exists('untilDate');

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($matchAll)
            ->addMustNot($untilQuery)
            ->addFilter($geoQuery);

        $query = new \Elastica\Query();
        $query->setQuery($boolQuery);

        $query->setSize(25);
        $query->setSort(
            [
                '_geo_distance' =>
                    [
                        'pin' =>
                            [
                                'lat' => $this->coord->getLatitude(),
                                'lon' => $this->coord->getLongitude()
                            ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
            ]
        );

        $results = $this->stationFinder->find($query);

        return $results;
    }
}
