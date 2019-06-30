<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Station;
use App\Pollution\DataCache\DataCacheInterface;
use App\Pollution\DataCache\KeyGenerator;
use Caldera\GeoBasic\Coord\CoordInterface;
use FOS\ElasticaBundle\Finder\FinderInterface;

class CachedElasticDataRetriever implements DataRetrieverInterface
{
    /** @var FinderInterface $dataFinder */
    protected $dataFinder;

    /** @var DataCacheInterface $dataCache */
    protected $dataCache;

    public function __construct(DataCacheInterface $dataCache, FinderInterface $dataFinder)
    {
        $this->dataFinder = $dataFinder;
        $this->dataCache = $dataCache;
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 750): array
    {
        $stationList = $this->getStationList($coord, $maxDistance, $maxResults);
        $dataList = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $key = KeyGenerator::generateKeyForStationAndPollutant($station, $pollutantId);

            $data = $this->dataCache->getData($key);

            $dataList[] = $data;
        }

        return $dataList;
    }

    protected function getStationList(CoordInterface $coord, float $maxDistance, int $maxResults): array
    {
        if ($coord instanceof Station) {
            $stationQuery = new \Elastica\Query\Nested();
            $stationQuery->setPath('station');
            $stationQuery->setQuery(new \Elastica\Query\Term(['station.id' => $coord->getId()]));
        } else {
            $stationGeoQuery = new \Elastica\Query\GeoDistance('station.pin', [
                'lat' => $coord->getLatitude(),
                'lon' => $coord->getLongitude(),
            ],
                sprintf('%fkm', $maxDistance));

            $stationQuery = new \Elastica\Query\Nested();
            $stationQuery->setPath('station');
            $stationQuery->setQuery($stationGeoQuery);
        }

        $query = new \Elastica\Query($stationQuery);

        $query
            ->addSort([
                '_geo_distance' => [
                    'station.pin' => [
                        'lat' => $coord->getLatitude(),
                        'lon' => $coord->getLongitude()
                    ],
                    'order' => 'asc',
                    'unit' => 'km',
                    'nested_path' => 'station',
                ]
            ])
            ->addSort(['dateTime' => 'desc']);

        $query->setSize($maxResults);

        return $this->dataFinder->find($query);
    }
}
