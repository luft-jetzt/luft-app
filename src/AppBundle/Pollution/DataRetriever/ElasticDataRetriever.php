<?php

namespace AppBundle\Pollution\DataRetriever;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use FOS\ElasticaBundle\Finder\FinderInterface;

class ElasticDataRetriever implements DataRetrieverInterface
{
    protected $dataFinder;

    public function __construct(FinderInterface $dataFinder)
    {
        $this->dataFinder = $dataFinder;
    }

    public function retrieveStationData(Station $station, string $pollutant): ?Data
    {
        $simpleQuery = new \Elastica\Query\SimpleQueryString($station->__toString());
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => $pollutant]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($simpleQuery)
        ;

        $query = new \Elastica\Query($boolQuery);
        $query
            ->setSort(['dateTimeFormatted' => ['order' => 'desc']])
            ->setSize(1)
        ;

        $results = $this->dataFinder->find($query);

        if (count($results) === 1) {
            return array_pop($results);
        }

        return null;
    }
}
