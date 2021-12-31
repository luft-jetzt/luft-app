<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Pollution\DataFinder\ElasticFinder;
use Caldera\GeoBasic\Coord\CoordInterface;
use Elastica\Aggregation\DateHistogram;

class ValueFetcher implements ValueFetcherInterface
{
    protected ElasticFinder $finder;

    public function __construct(ElasticFinder $finder)
    {
        $this->finder = $finder;
    }

    public function fetchValues(CoordInterface $coord, float $maxDistance = 15): array
    {
        $query = new \Elastica\Query(new \Elastica\Query\MatchAll());
        $query->setSize(0);

        $dateTimeAggregation = new \Elastica\Aggregation\Range('datetime_agg');
        $dateTimeAggregation->setField('dateTime');
        $dateTimeAggregation->addRange(
            '2021-12-31 11:00:00',
            '2022-01-01 12:00:00'
        );
        $dateTimeAggregation->addRange(
            '2020-12-31 11:00:00',
            '2021-01-01 12:00:00'
        );
        $dateTimeAggregation->addRange(
            '2019-12-31 11:00:00',
            '2020-01-01 12:00:00'
        );

        $query->addAggregation($dateTimeAggregation);

        $pollutantAggregation = new \Elastica\Aggregation\Terms('pollutant_agg');
        $pollutantAggregation->setField('pollutant');

        $dateTimeAggregation->addAggregation($pollutantAggregation);

        $providerAggregation = new \Elastica\Aggregation\Terms('provider_agg');
        $providerAggregation->setField('provider');
        $pollutantAggregation->addAggregation($providerAggregation);

        $geodistanceAggregation = new \Elastica\Aggregation\GeoDistance(
            'geodistance_agg',
            'pin',
            $coord->toLatLonArray()
        );
        $geodistanceAggregation->addRange(null, $maxDistance);
        $geodistanceAggregation->setUnit('km');

        $providerAggregation->addAggregation($geodistanceAggregation);

        $histogramAggregation = new DateHistogram('date_histogram_agg', 'dateTime', '30m');

        $geodistanceAggregation->addAggregation($histogramAggregation);

        $topHitsAggregation = new \Elastica\Aggregation\TopHits('top_hits_agg');
        $topHitsAggregation->setSize(1);
        $topHitsAggregation->setSort([[
            '_geo_distance' => [
                'pin' => [
                    'lat' => $coord->getLatitude(),
                    'lon' => $coord->getLongitude(),
                ],
                'order' => 'ASC',
                'unit' => 'km',
            ],
            'dateTime' => 'DESC',
        ],
        ]);

        $histogramAggregation->addAggregation($topHitsAggregation);

        $result = $this->finder->find($query);

        return $result;
    }
}
