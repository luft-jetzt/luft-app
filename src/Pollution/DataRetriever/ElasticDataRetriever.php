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

    public function retrieveStationData(Station $station, int $pollutant, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, string $order = 'DESC'): ?Data
    {
        $stationQuery = new \Elastica\Query\Nested();
        $stationQuery->setPath('station');
        $stationQuery->setQuery(new \Elastica\Query\Term(['station.id' => $station->getId()]));

        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => $pollutant]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($stationQuery);

        if ($fromDateTime && $dateInterval) {
            $untilDateTime = clone $fromDateTime;
            $untilDateTime->add($dateInterval);

            $dateTimeQuery = new \Elastica\Query\Range('dateTime', ['gt' => $fromDateTime->format('Y-m-d H:i:s'), 'lte' => $untilDateTime->format('Y-m-d H:i:s'), 'format' => 'yyyy-MM-dd HH:mm:ss']);

            $boolQuery->addMust($dateTimeQuery);
        }

        $query = new \Elastica\Query($boolQuery);
        $query
            ->setSort(['dateTime' => ['order' => strtolower($order)]])
            ->setSize(1);

        $results = $this->dataFinder->find($query);

        if (count($results) === 1) {
            return array_pop($results);
        }

        return null;
    }
}
