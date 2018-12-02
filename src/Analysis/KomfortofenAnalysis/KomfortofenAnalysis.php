<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Entity\Station;
use App\Pollution\Pollutant\PollutantInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class KomfortofenAnalysis implements KomfortofenAnalysisInterface
{
    /** @var Station $station */
    protected $station;

    /** @var PollutantInterface $pollutant */
    protected $pollutant;

    /** @var PaginatedFinderInterface $finder */
    protected $finder;

    /** @var \DateTimeInterface $fromDateTime */
    protected $fromDateTime;

    /** @var \DateTimeInterface $untilDateTime */
    protected $untilDateTime;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function setStation(Station $station): KomfortofenAnalysisInterface
    {
        $this->station = $station;

        return $this;
    }

    public function setPollutant(PollutantInterface $pollutant): KomfortofenAnalysisInterface
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function setFromDateTime(\DateTimeInterface $fromDateTime): KomfortofenAnalysisInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function setUntilDateTime(\DateTimeInterface $untilDateTime): KomfortofenAnalysisInterface
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    protected function convertToList(array $buckets): array
    {
        $resultList = [];

        /** @var array $bucket */
        foreach ($buckets as $bucket) {
            $resultList[] = ['date' => $bucket['key_as_string'], 'value' => array_pop($bucket['max_value'])];
        }

        return $resultList;
    }

    public function analyze(): array
    {
        $stationQuery = new \Elastica\Query\Term(['station' => $this->station->getId()]);
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => 1]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($stationQuery);

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
            'gt' => $this->fromDateTime->format('Y-m-d H:i:s'),
            'lte' => $this->untilDateTime->format('Y-m-d H:i:s'),
            'format' => 'yyyy-MM-dd HH:mm:ss'
        ]);

        $boolQuery->addMust($dateTimeQuery);

        $histogram = new \Elastica\Aggregation\DateHistogram('value_bucket', 'dateTime', '1D');
        $histogram->setTimezone('Europe/Berlin');
        $histogram->setFormat('yyyy-MM-dd');

        $max = new \Elastica\Aggregation\Max('max_value');
        $max->setField('value');

        $query = new \Elastica\Query($boolQuery);
        $query->addAggregation($histogram);
        $histogram->addAggregation($max);

        $results = $this->finder->findPaginated($query);

        $buckets = $results->getAdapter()->getAggregations();

        return $this->convertToList($buckets['value_bucket']['buckets']);
    }
}
