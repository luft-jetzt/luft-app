<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\StationCache\StationCacheInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Elastica\Query;
use Elastica\Result;
use Elastica\SearchableInterface;

class ElasticDataRetriever implements DataRetrieverInterface
{
    protected StationCacheInterface $stationCache;

    protected SearchableInterface $searchable;

    public function __construct(SearchableInterface $searchable, StationCacheInterface $stationCache)
    {
        $this->searchable = $searchable;
        $this->stationCache = $stationCache;
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 750): array
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

        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => $pollutantId]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            //->addMust($pollutantQuery)
            //->addMust($stationQuery)
        ;
/*
        if ($fromDateTime && $dateInterval) {
            $untilDateTime = clone $fromDateTime;
            $untilDateTime->add($dateInterval);

            $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
                'gt' => $fromDateTime->format('Y-m-d H:i:s'),
                'lte' => $untilDateTime->format('Y-m-d H:i:s'),
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ]);

            $boolQuery->addMust($dateTimeQuery);
        }
*/
        $query = new \Elastica\Query($boolQuery);

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

        $resultList = $this->find($query);

        /** @var Result $elasticResult */
        foreach ($resultList as $key => $elasticResult) {
            $data = new Data();

            $data
                ->setValue($elasticResult->getData()['value'])
                ->setPollutant($elasticResult->getData()['pollutant'])
                ->setStation($this->stationCache->getStationByCode($elasticResult->getData()['station']['stationCode']))
                ->setDateTime(new \DateTime($elasticResult->getData()['dateTime']))
            ;

            $resultList[$key] = $data;
        }

        return $resultList;
    }

    public function find($query, $limit = null, $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }

        $results = $this->searchable->search($queryObject, $options)->getResults();

        return $results;
    }
}
