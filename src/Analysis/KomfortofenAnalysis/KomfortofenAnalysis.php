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
            if (array_key_exists('derivative_agg', $bucket)) {
                $resultList[] = [
                    'dateTime' => $bucket['key_as_string'],
                    'value' => array_pop($bucket['max_agg']),
                    'derivative' => array_pop($bucket['derivative_agg'])
                ];
            }
        }

        return $resultList;
    }

    public function analyze(): array
    {
        $stationQuery = new \Elastica\Query\Term(['station' => $this->station->getId()]);
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => 1]);

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
            'gt' => $this->fromDateTime->format('Y-m-d H:i:s'),
            'lte' => $this->untilDateTime->format('Y-m-d H:i:s'),
            'format' => 'yyyy-MM-dd HH:mm:ss'
        ]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($stationQuery)
            ->addMust($dateTimeQuery);

        $histogram = new \Elastica\Aggregation\DateHistogram('histogram_agg', 'dateTime', '1H');
        $histogram->setTimezone('Europe/Berlin');
        $histogram->setFormat('yyyy-MM-dd HH:mm:ss');

        $max = new \Elastica\Aggregation\Max('max_agg');
        $max->setField('value');
        $histogram->addAggregation($max);

        $derive = new \Elastica\Aggregation\Derivative('derivative_agg');
        $derive->setBucketsPath('max_agg');
        $histogram->addAggregation($derive);

        $bucketSelector = new \Elastica\Aggregation\BucketSelector('bucket_selector');
        $bucketSelector->setBucketsPath(['my_var' => 'derivative_agg']);
        $bucketSelector->setGapPolicy('skip');
        $bucketSelector->setScript('params.my_var != null && params.my_var > 10');
        $histogram->addAggregation($bucketSelector);

        $query = new \Elastica\Query($boolQuery);
        $query->addAggregation($histogram);

        //echo json_encode($query->toArray());

        $results = $this->finder->findPaginated($query);

        $buckets = $results->getAdapter()->getAggregations();

        return $this->convertToList($buckets['histogram_agg']['buckets']);
    }
}
