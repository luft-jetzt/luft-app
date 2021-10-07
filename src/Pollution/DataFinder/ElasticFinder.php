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

        if (count($aggregation) > 0) {
            $pollutantBuckets = $aggregation['buckets'];

            if (count($pollutantBuckets) > 0) {
                $providerBuckets = $pollutantBuckets[0]['provider_agg']['buckets'];

                foreach ($providerBuckets as $providerBucket) {
                    $topHits = $providerBucket['top_hits_agg']['hits']['hits'][0]['_source'];

                    dump($topHits);
                }
            }
        }

        $resultList = $this->searchable->search($queryObject, $options)->getResults();

        foreach ($resultList as $key => $result) {
            $data = $this->dataConverter->convert($result);
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
}
