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

        return $results->getAdapter()->getAggregations();
    }
}
