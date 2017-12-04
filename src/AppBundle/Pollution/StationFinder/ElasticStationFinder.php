<?php

namespace AppBundle\Pollution\StationFinder;

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
        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $this->coord->getLatitude(),
                'lon' => $this->coord->getLongitude()
            ],
            '20km'
        );

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $geoFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(15);
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