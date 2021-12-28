<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Air\Measurement\MeasurementInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Elastica\Query\BoolQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class ValueFetcher implements ValueFetcherInterface
{
    protected PaginatedFinderInterface $finder;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function fetchValues(CoordInterface $coord, float $maxDistance = 15): array
    {
        $stationGeoQuery = new \Elastica\Query\GeoDistance('station.pin', [
            'lat' => $coord->getLatitude(),
            'lon' => $coord->getLongitude(),
        ],
            sprintf('%fkm', $maxDistance));

        $stationQuery = new \Elastica\Query\Nested();
        $stationQuery->setPath('station');
        $stationQuery->setQuery($stationGeoQuery);

        $pm10Query = new \Elastica\Query\Term(['pollutant' => MeasurementInterface::MEASUREMENT_PM10]);
        //$pm25Query = new \Elastica\Query\Term(['pollutant' => PollutantInterface::POLLUTANT_PM25]);

        $pollutantQuery = new BoolQuery();
        $pollutantQuery->addShould($pm10Query);
        //$pollutantQuery->addShould($pm25Query);

        $fromDateTime = new Carbon(sprintf('%d-12-31 11:00:00', $year));
        $untilDateTime = $fromDateTime->copy()->addHours(36);

        $rangeQuery = new \Elastica\Query\Range('dateTime', [
            'gt' => $fromDateTime->format('Y-m-d H:i:s'),
            'lte' => $untilDateTime->format('Y-m-d H:i:s'),
            'format' => 'yyyy-MM-dd HH:mm:ss'
        ]);

        $providerQuery = new \Elastica\Query\Term(['provider' => 'uba_de']);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($rangeQuery)
            //         ->addMust($providerQuery)
            ->addMust($stationQuery);

        $query = new \Elastica\Query($boolQuery);

        $query
            ->addSort([
                '_geo_distance' => [
                    'pin' => [
                        'lat' => $coord->getLatitude(),
                        'lon' => $coord->getLongitude()
                    ],
                    'order' => 'asc',
                    'unit' => 'km',
                ]
            ]);

        return $this->finder->find($query, 1000);
    }
}