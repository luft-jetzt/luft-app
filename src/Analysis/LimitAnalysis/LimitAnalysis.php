<?php declare(strict_types=1);

namespace App\Analysis\LimitAnalysis;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Station;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class LimitAnalysis implements LimitAnalysisInterface
{
    protected ?Station $station = null;
    protected ?MeasurementInterface $measurement = null;
    protected ?\DateTimeInterface $fromDateTime = null;
    protected ?\DateTimeInterface $untilDateTime = null;

    public function __construct(protected ?\FOS\ElasticaBundle\Finder\PaginatedFinderInterface $finder)
    {
    }

    #[\Override]
    public function setStation(Station $station): LimitAnalysisInterface
    {
        $this->station = $station;

        return $this;
    }

    #[\Override]
    public function setMeasurement(MeasurementInterface $measurement): LimitAnalysisInterface
    {
        $this->measurement = $measurement;

        return $this;
    }

    #[\Override]
    public function setFromDateTime(\DateTimeInterface $fromDateTime): LimitAnalysisInterface
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    #[\Override]
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
            $resultList[] = ['date' => $bucket['key_as_string'], 'value' => array_pop($bucket['max_value'])];
        }

        return $resultList;
    }

    #[\Override]
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
