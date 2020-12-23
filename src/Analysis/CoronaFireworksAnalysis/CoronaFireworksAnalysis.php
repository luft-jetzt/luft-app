<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Air\Measurement\MeasurementInterface;
use App\Air\ViewModelFactory\DistanceCalculator;
use App\Analysis\FireworksAnalysis\FireworksModelFactoryInterface;
use App\Entity\Data;
use App\Pollution\PollutionDataFactory\PollutionDataFactoryInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Elastica\Query\BoolQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class CoronaFireworksAnalysis implements CoronaFireworksAnalysisInterface
{
    protected PaginatedFinderInterface $finder;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function analyze(CoordInterface $coord): array
    {
        $yearList = $this->initYearList();
        $valueList = $this->fetchValues($coord);

        foreach ($yearList as $year => $hourList) {
            $startDatTime = $this->calculateStartDateTime($year);

            foreach ($hourList as $minutesSinceStartDateTime => $data) {
                $dateTime = $startDatTime->copy()->addMinutes($minutesSinceStartDateTime);
                $candidateList = [];

                /** @var Data $candidate */
                foreach ($valueList as $key => $candidate) {
                    if ($dateTime->diffInMinutes($candidate->getDateTime()) < 30) {
                        $candidateList[$key] = $candidate;
                    }
                }

                $minDistance = null;
                $nearestData = null;

                foreach ($candidateList as $candidate) {
                    $distance = DistanceCalculator::distance($coord, $candidate->getStation());

                    if (!$minDistance || $distance < $minDistance) {
                        $minDistance = $distance;
                        $nearestData = $candidate;
                    }
                }

                $yearList[$year][$minutesSinceStartDateTime] = $nearestData;

                foreach ($candidateList as $key => $deleteableCandidate) {
                    unset($valueList[$key]);
                }
            }
        }

        return $yearList;
    }

    protected function initYearList(): array
    {
        $year = (new Carbon())->subDays(360);
        $yearList = [];

        for ($yearSub = 0; $yearSub <= 2; ++$yearSub) {
            $yearList[$year->year] = [];
            $year->subYear();
        }

        foreach ($yearList as $year => $hourList) {
            $startDateTime = $this->calculateStartDateTime($year);
            $endDateTime = $startDateTime->copy()->addHours(36);

            $dateTime = $endDateTime->copy();

            do {
                $yearList[$year][$dateTime->diffInMinutes($startDateTime)] = null;
                $dateTime->subMinutes(60);
            } while ($dateTime > $startDateTime);
        }

        return $yearList;
    }

    protected function fetchValues(CoordInterface $coord, float $maxDistance = 100.0): array
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

        $dateTimeQuery = $this->createDateTimeQuery();

        $providerQuery = new \Elastica\Query\Term(['provider' => 'uba_de']);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($pollutantQuery)
            ->addMust($dateTimeQuery)
            ->addMust($providerQuery)
            ->addMust($stationQuery);

        $query = new \Elastica\Query($boolQuery);

        return $this->finder->find($query, 5000);
    }

    protected function createDateTimeQuery(): BoolQuery
    {
        $currentYear = (new Carbon())->year;
        $years = range($currentYear - 4, $currentYear + 1);

        $dateTimeQuery = new BoolQuery();

        foreach ($years as $year) {
            $fromDateTime = new Carbon(sprintf('%d-12-31 12:00:00', $year));
            $untilDateTime = $fromDateTime->copy()->addHours(36);

            $rangeQuery = new \Elastica\Query\Range('dateTime', [
                'gt' => $fromDateTime->format('Y-m-d H:i:s'),
                'lte' => $untilDateTime->format('Y-m-d H:i:s'),
                'format' => 'yyyy-MM-dd HH:mm:ss'
            ]);

            $dateTimeQuery->addShould($rangeQuery);
        }

        return $dateTimeQuery;
    }

    protected function calculateStartDateTime(int $year): Carbon
    {
        $startDateTimeSpec = '%d-12-31 12:00:00';
        return new Carbon(sprintf($startDateTimeSpec, $year));
    }
}