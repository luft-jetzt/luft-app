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
    protected DataConverterInterface $dataConverter;

    public function __construct(SearchableInterface $searchable, DataConverterInterface $dataConverter)
    {
        $this->searchable = $searchable;
        $this->dataConverter = $dataConverter;
    }

    public function find(Query $query, int $limit = null, array $options = []): array
    {
        $queryObject = Query::create($query);
        if (null !== $limit) {
            $queryObject->setSize($limit);
        }

        $aggregation = $this->searchable->search($queryObject, $options)->getAggregation('pollutant_agg');

        $resultList = $this->unfoldAggregation($aggregation);

        foreach ($resultList as $key => $result) {
            $data = $this->dataConverter->convertArray($result);

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

    protected function unfoldAggregation(array $aggregation): array
    {
        $resultList = [];

        if (array_key_exists('buckets', $aggregation)) {
            foreach ($aggregation['buckets'] as $bucket) {
                $resultList = array_merge($resultList, $this->unfoldAggregation($bucket));
            }
        } elseif (array_key_exists('hits', $aggregation)) {
            $hitsList = $aggregation['hits']['hits'];

            $firstHit = array_pop($hitsList);

            $resultList[] = $firstHit['_source'];
        } else {
            foreach ($aggregation as $key => $property) {
                if (str_ends_with($key, '_agg')) {
                    $resultList = array_merge($resultList, $this->unfoldAggregation($property));
                }
            }
        }

        return $resultList;
    }
}
