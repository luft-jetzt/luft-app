<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Pollution\DataFinder\ElasticFinder;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Elastica\Aggregation\DateHistogram;

class ValueFetcher implements ValueFetcherInterface
{
    protected ElasticFinder $finder;

    public function __construct(ElasticFinder $finder)
    {
        $this->finder = $finder;
    }

    public function fetchValues(CoordInterface $coord, array $yearList = [], int $startHour = 12, int $rangeInMinutes = 1440, float $maxDistance = 15): array
    {
        $query = new \Elastica\Query(new \Elastica\Query\MatchAll());
        $query->setSize(0);

        $dateTimeAggregation = new \Elastica\Aggregation\Range('datetime_agg');
        $dateTimeAggregation->setField('dateTime');

        foreach ($yearList as $year) {
            $dateTimeSpec = sprintf('%d-12-31 %d:00:00', $year, $startHour);
            $startDateTime = new Carbon($dateTimeSpec, new CarbonTimeZone('UTC'));
            $untilDateTime = $startDateTime->copy()->addMinutes($rangeInMinutes);

            $dateTimeAggregation->addRange(
                $startDateTime->format('Y-m-d H:i:s'),
                $untilDateTime->format('Y-m-d H:i:s'),
            );
        }

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
            [
                'value' => [
                    'order' => 'DESC',
                ]
            ]
        ]);

        $histogramAggregation->addAggregation($topHitsAggregation);

        $result = $this->finder->find($query);

        return $result;
    }
}
