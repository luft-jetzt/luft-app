<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\Measurement\MeasurementInterface;
use App\Air\Measurement\PM10;
use App\Air\ViewModel\MeasurementViewModel;
use App\Air\ViewModelFactory\DistanceCalculator;
use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
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
    protected MeasurementViewModelFactoryInterface $measurementViewModelFactory;
    protected AirQualityCalculatorInterface $airQualityCalculator;

    public function __construct(PaginatedFinderInterface $finder, MeasurementViewModelFactoryInterface $measurementViewModelFactory, AirQualityCalculatorInterface $airQualityCalculator)
    {
        $this->finder = $finder;
        $this->measurementViewModelFactory = $measurementViewModelFactory;
        $this->airQualityCalculator = $airQualityCalculator;
    }

    public function analyze(CoordInterface $coord): array
    {
        $yearList = $this->initYearList();

        foreach ($yearList as $year => $hourList) {
            $valueList = $this->fetchValues($coord, $year);

            $startDatTime = StartDateTimeCalculator::calculateStartDateTime($year);

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

                if ($nearestData) {
                    $yearList[$year][$minutesSinceStartDateTime] = $this->decorateData($nearestData, $coord);
                }

                foreach ($candidateList as $key => $deleteableCandidate) {
                    unset($valueList[$key]);
                }
            }
        }

        return $yearList;
    }

    /**
     * @todo Use ViewModelFactory for this
     */
    protected function decorateData(Data $data, CoordInterface $coord): MeasurementViewModel
    {
        $viewModel = new MeasurementViewModel($data);
        $viewModel
            ->setStation($data->getStation())
            ->setMeasurement(new PM10())
            ->setDistance(DistanceCalculator::distance($coord, $data->getStation()))
            ->setPollutionLevel($this->airQualityCalculator->calculateViewModel($viewModel))
        ;

        return $viewModel;
    }

    protected function initYearList(): array
    {
        $year = StartDateTimeCalculator::calculateStartDateTime();
        $yearList = [];

        for ($yearSub = 0; $yearSub <= 2; ++$yearSub) {
            $yearList[$year->year] = [];
            $year->subYear();
        }

        foreach ($yearList as $year => $hourList) {
            $startDateTime = StartDateTimeCalculator::calculateStartDateTime($year);
            $endDateTime = $startDateTime->copy()->addHours(36);

            $dateTime = $endDateTime->copy();

            do {
                $yearList[$year][$dateTime->diffInMinutes($startDateTime)] = null;
                $dateTime->subMinutes(60);
            } while ($dateTime > $startDateTime);
        }

        return $yearList;
    }

    protected function fetchValues(CoordInterface $coord, int $year, float $maxDistance = 100.0): array
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

        $fromDateTime = new Carbon(sprintf('%d-12-31 12:00:00', $year));
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
                    'station.pin' => [
                        'lat' => $coord->getLatitude(),
                        'lon' => $coord->getLongitude()
                    ],
                    'order' => 'asc',
                    'unit' => 'km',
                    'nested_path' => 'station',
                ]
            ]);

        return $this->finder->find($query, 500);
    }
}