<?php declare(strict_types=1);

namespace App\Analysis\LimitAnalysis;

use App\Entity\Station;
use App\Pollution\Pollutant\PollutantInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class LimitAnalysis implements LimitAnalysisInterface
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

    public function setStation(Station $station): LimitAnalysisInterface
    {
        $this->station = $station;

        return $this;
    }

    public function setPollutant(PollutantInterface $pollutant): LimitAnalysisInterface
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function setFromDateTime(\DateTimeInterface $fromDateTime): LimitAnalysisInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function setUntilDateTime(\DateTimeInterface $untilDateTime): LimitAnalysisInterface
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    protected function convertToList(array $buckets): array
    {
        $resultList = [];

        /** @var array $bucket */
        foreach ($buckets as $bucket) {
            $resultList[] = ['date' => $bucket['key_as_string'], 'value' => array_pop($bucket['avg_agg'])];
        }

        return $resultList;
    }

    public function analyze(): array
    {
        $stationQuery = new \Elastica\Query\Term(['station' => $this->station->getId()]);
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => PollutantInterface::POLLUTANT_PM10]);

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

        $histogram = new \Elastica\Aggregation\DateHistogram('histogram_agg', 'dateTime', '1D');
        $histogram->setTimezone('Europe/Berlin');
        $histogram->setFormat('yyyy-MM-dd');

        $avgAgg = new \Elastica\Aggregation\Avg('avg_agg');
        $avgAgg->setField('value');
        $histogram->addAggregation($avgAgg);

        $query = new \Elastica\Query($boolQuery);
        $query->addAggregation($histogram);

        $results = $this->finder->findPaginated($query);
        $buckets = $results->getAdapter()->getAggregations();

        return $this->convertToList($buckets['histogram_agg']['buckets']);
    }
}
