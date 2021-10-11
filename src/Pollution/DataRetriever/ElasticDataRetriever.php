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
        $query = new \Elastica\Query(new \Elastica\Query\MatchAll());
        $query->setSize(0);

        $pollutantAggregation = new \Elastica\Aggregation\Terms('pollutant_agg');
        $pollutantAggregation->setField('pollutant');
        $query->addAggregation($pollutantAggregation);

        if ($coord instanceof Station) {
            $stationAggregation = new \Elastica\Aggregation\Terms('station_agg');
            $stationAggregation->setField('stationCode');
            $stationAggregation->setInclude($coord->getStationCode());

            $pollutantAggregation->addAggregation($stationAggregation);
        } else {
            $providerAggregation = new \Elastica\Aggregation\Terms('provider_agg');
            $providerAggregation->setField('provider');
            $pollutantAggregation->addAggregation($providerAggregation);
        }

        $topHitsAggregation = new \Elastica\Aggregation\TopHits('top_hits_agg');
        $topHitsAggregation->setSort([
            '_geo_distance' => [
                'station.pin' => [
                    'lat' => $coord->getLatitude(),
                    'lon' => $coord->getLongitude()
                ],
                'order' => 'asc',
                'unit' => 'km',
                'nested_path' => 'station',
            ],
            'dateTime' => 'DESC',
        ]);

        if ($coord instanceof Station) {
            $stationAggregation->addAggregation($topHitsAggregation);
        } else {
            $providerAggregation->addAggregation($topHitsAggregation);
        }

        $result = $this->finder->find($query);

        return $result;
    }
}
