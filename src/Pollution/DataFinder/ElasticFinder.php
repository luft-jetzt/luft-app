<?php declare(strict_types=1);

namespace App\Pollution\DataFinder;

use App\Entity\Data;
use App\Pollution\StationCache\StationCacheInterface;
use Elastica\Query;
use Elastica\Result;
use Elastica\SearchableInterface;

class ElasticFinder implements FinderInterface
{
    protected SearchableInterface $searchable;
    protected StationCacheInterface $stationCache;

    public function __construct(SearchableInterface $searchable, StationCacheInterface $stationCache)
    {
        $this->searchable = $searchable;
        $this->stationCache = $stationCache;
    }

    public function find(Query $query, int $limit = null, array $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }

        $resultList = $this->searchable->search($queryObject, $options)->getResults();

        foreach ($resultList as $key => $result) {
            $data = $this->convertToData($result);
            if ($data) {
                $resultList[$key] = $data;
            } else {
                unset($resultList[$key]);
            }
        }

        return $resultList;
    }

    public function findAggregations(Query $query, int $limit = null, array $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }

        $result = $this->searchable->search($queryObject, $options)->getAggregations();

        return $result;
    }

    protected function convertToData(Result $elasticResult): ?Data
    {
        $data = new Data();

        $station = $this->stationCache->getStationByCode($elasticResult->getData()['station']['stationCode']);

        if (!$station) {
            return null;
        }

        $data
            ->setValue($elasticResult->getData()['value'])
            ->setPollutant($elasticResult->getData()['pollutant'])
            ->setStation($station)
            ->setDateTime(new \DateTime($elasticResult->getData()['dateTime']))
        ;

        return $data;
    }
}
