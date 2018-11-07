<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Data;
use App\Entity\Station;
use FOS\ElasticaBundle\Finder\FinderInterface;

class ElasticDataRetriever implements DataRetrieverInterface
{
    /** @var FinderInterface $dataFinder */
    protected $dataFinder;

    public function __construct(FinderInterface $dataFinder)
    {
        $this->dataFinder = $dataFinder;
    }

    public function retrieveStationData(Station $station, int $pollutant, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null): ?Data
    {
        $stationQuery = new \Elastica\Query\Term(['station' => $station->getId()]);
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => $pollutant]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($stationQuery);

        if ($fromDateTime && $dateInterval) {

            $dateTimeQuery = new \Elastica\Query\Range([]);

            $boolQuery->addMust($dateTimeQuery);
        }

        $query = new \Elastica\Query($boolQuery);
        $query
            ->setSort(['dateTime' => ['order' => 'desc']])
            ->setSize(1);

        $results = $this->dataFinder->find($query);

        if (count($results) === 1) {
            return array_pop($results);
        }

        return null;
    }
}
