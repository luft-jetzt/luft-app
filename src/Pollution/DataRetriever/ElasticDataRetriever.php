<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Station;
use App\Pollution\DataFinder\ElasticFinder;
use App\Pollution\StationCache\StationCacheInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

class ElasticDataRetriever implements DataRetrieverInterface
{
    protected StationCacheInterface $stationCache;

    protected ElasticFinder $finder;

    public function __construct(ElasticFinder $finder, StationCacheInterface $stationCache)
    {
        $this->finder = $finder;
        $this->stationCache = $stationCache;
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 750): array
    {
        $fromDateTime = new \DateTime();
        $fromDateTime->sub(new \DateInterval('PT8H'));

        $query = new \Elastica\Query(new \Elastica\Query\MatchAll());
        $query->setSize(0);

        if ($fromDateTime && $dateInterval) {
            $untilDateTime = (clone $fromDateTime)->add($dateInterval);
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

        if ($coord instanceof Station) {
            $stationAggregation->addAggregation($topHitsAggregation);
        } else {
            $geodistanceAggregation->addAggregation($topHitsAggregation);
        }

        //dd(json_encode($query->toArray()));
        $result = $this->finder->find($query);

        return $result;
    }
}
