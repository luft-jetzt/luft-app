<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Pollution\Pollutant\PollutantInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class KomfortofenAnalysis implements KomfortofenAnalysisInterface
{
    /** @var float $minSlope */
    protected $minSlope = 80.0;

    /** @var float $maxSlope */
    protected $maxSlope = 300.0;

    /** @var PollutantInterface $pollutant */
    protected $pollutant;

    /** @var PaginatedFinderInterface $finder */
    protected $finder;

    /** @var \DateTimeInterface $fromDateTime */
    protected $fromDateTime;

    /** @var \DateTimeInterface $untilDateTime */
    protected $untilDateTime;

    /** @var KomfortofenModelFactoryInterface $komfortofenModelFactory */
    protected $komfortofenModelFactory;

    public function __construct(PaginatedFinderInterface $finder, KomfortofenModelFactoryInterface $komfortofenModelFactory)
    {
        $this->finder = $finder;
        $this->komfortofenModelFactory = $komfortofenModelFactory;
    }

    public function setMinSlope(float $minSlope): KomfortofenAnalysisInterface
    {
        $this->minSlope = $minSlope;

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

    public function setMaxSlope(float $maxSlope): KomfortofenAnalysisInterface
    {
        $this->maxSlope = $maxSlope;

        return $this;
    }

    protected function convertToList(array $buckets): array
    {
        return $this->komfortofenModelFactory->convert($buckets);
    }

    public function analyze(): array
    {
        $pollutantQuery = new \Elastica\Query\Term(['pollutant' => 1]);

        $dateTimeQuery = new \Elastica\Query\Range('dateTime', [
            'gt' => $this->fromDateTime->format('Y-m-d H:i:s'),
            'lte' => $this->untilDateTime->format('Y-m-d H:i:s'),
            'format' => 'yyyy-MM-dd HH:mm:ss'
        ]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($dateTimeQuery);

        $histogram = new \Elastica\Aggregation\DateHistogram('histogram_agg', 'dateTime', 'hour');
        $histogram->setTimezone('Europe/Berlin');
        $histogram->setFormat('yyyy-MM-dd HH:mm:ss');

        $termAgg = new \Elastica\Aggregation\Terms('station_agg');
        $termAgg->setField('station');
        $termAgg->addAggregation($histogram);

        $max = new \Elastica\Aggregation\Max('max_agg');
        $max->setField('value');
        $histogram->addAggregation($max);

        $derive = new \Elastica\Aggregation\Derivative('derivative_agg');
        $derive->setBucketsPath('max_agg');
        $histogram->addAggregation($derive);

        $bucketSelector = new \Elastica\Aggregation\BucketSelector('bucket_selector');
        $bucketSelector->setBucketsPath(['my_var' => 'derivative_agg']);
        $bucketSelector->setGapPolicy('skip');
        $bucketSelector->setScript(sprintf('params.my_var != null && params.my_var > %f && params.my_var < %f', $this->minSlope, $this->maxSlope));
        $histogram->addAggregation($bucketSelector);

        $topHistsAgg = new \Elastica\Aggregation\TopHits('top_hits_agg');
        $topHistsAgg->setSize(1);
        $topHistsAgg->setSort(['value' => ['order' => 'desc']]);
        $histogram->addAggregation($topHistsAgg);

        $query = new \Elastica\Query($boolQuery);
        $query->addAggregation($histogram);

        $results = $this->finder->findPaginated($query);

        $buckets = $results->getAdapter()->getAggregations();

        return $this->convertToList($buckets['histogram_agg']['buckets']);
    }
}
