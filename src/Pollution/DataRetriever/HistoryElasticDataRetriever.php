<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Station;
use App\Pollution\DataFinder\ElasticFinder;
use App\Pollution\StationCache\StationCacheInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Elastica\Aggregation\DateHistogram;

/**
 * @todo Rebuild this for postigs
 */
class HistoryElasticDataRetriever implements DataRetrieverInterface
{
    public function __construct(protected StationCacheInterface $stationCache)
    {
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 750): array
    {
        $dateTimeAggregation = null;
        if (!$fromDateTime) {
            $fromDateTime = new \DateTime();
        }

        $query = new \Elastica\Query(new \Elastica\Query\MatchAll());
        $query->setSize(0);

        if ($fromDateTime && $dateInterval) {
            $untilDateTime = (clone $fromDateTime)->sub($dateInterval);
            $untilDateTime = new \DateTime();

            $dateTimeAggregation = new \Elastica\Aggregation\Range('datetime_agg');
            $dateTimeAggregation->setField('dateTime');
            $dateTimeAggregation->addRange(
                $fromDateTime->format('Y-m-d H:i:s'),
                $untilDateTime->format('Y-m-d H:i:s')
            );

            $query->addAggregation($dateTimeAggregation);
        }

        $pollutantAggregation = new \Elastica\Aggregation\Terms('pollutant_agg');
        $pollutantAggregation->setField('pollutant');

        if ($fromDateTime && $dateInterval) {
            $dateTimeAggregation->addAggregation($pollutantAggregation);
        } else {
            $query->addAggregation($pollutantAggregation);
        }

        if ($coord instanceof Station) {
            $stationAggregation = new \Elastica\Aggregation\Terms('station_agg');
            $stationAggregation->setField('stationCode');
            $stationAggregation->setInclude($coord->getStationCode());

            $pollutantAggregation->addAggregation($stationAggregation);
        } else {
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
        }

        $histogramAggregation = new DateHistogram('date_histogram_agg', 'dateTime', '1h');

        if ($coord instanceof Station) {
            $stationAggregation->addAggregation($histogramAggregation);
        } else {
            $geodistanceAggregation->addAggregation($histogramAggregation);
        }

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

        //dd(json_encode($query->toArray()));
        $result = $this->finder->find($query);

        return $result;
    }
}
