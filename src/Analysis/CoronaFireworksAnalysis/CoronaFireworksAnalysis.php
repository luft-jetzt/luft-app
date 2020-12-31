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
use Carbon\CarbonTimeZone;
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
            $dataList = $this->fetchValues($coord, $year);

            /**
             * @var Data $data
             * @todo get timezone handling done!!! This is really nasty
             */
            foreach ($dataList as $data) {
                if ('ld' === $data->getProvider()) {
                    $dateTime = Carbon::parse($data->getDateTime());

                    /**
                     * … but do not adjust datetime for current values directly from json api for the current year
                     * @todo FIX TIMEZONE HANDLING!!!
                     */
                    if ($dateTime->diffInDays(Carbon::now()) > 31) {
                        //$dateTime->subHour();
                        $data->setDateTime($dateTime);
                    }
                }
            }

            $startDatTime = StartDateTimeCalculator::calculateStartDateTime($year);

            foreach ($hourList as $minutesSinceStartDateTime => $data) {
                $dateTime = $startDatTime->copy()->addMinutes($minutesSinceStartDateTime);

                $candidateList = [];

                /** @var Data $candidate */
                foreach ($dataList as $key => $candidate) {
                    if ($dateTime->diffInMinutes($candidate->getDateTime()) <= 30 && $candidate->getDateTime() < $dateTime) {
                        $candidateList[$key] = $candidate;
                    }
                }

                $minDistance = null;
                $nearestData = null;

                foreach ($candidateList as $candidate) {
                    $distance = DistanceCalculator::distance($coord, $candidate->getStation());

                    if ($minDistance === null || $distance < $minDistance) {
                        $minDistance = $distance;
                        $nearestData = $candidate;
                    } elseif ($distance === $minDistance && $nearestData && $nearestData->getValue() < $candidate->getValue()) {
                        $nearestData = $candidate;
                    }
                }

                if ($nearestData) {
                    $yearList[$year][$minutesSinceStartDateTime] = $this->decorateData($nearestData, $coord);
                }

                foreach ($candidateList as $key => $deleteableCandidate) {
                    unset($dataList[$key]);
                }
            }
        }

        /**
         * @todo quick fix to hide future values
         */
        $startDateTime2020 = new Carbon('2020-12-31 12:00:00', new CarbonTimeZone('Europe/Berlin'));

        foreach ($yearList as $year => $hourList) {
            foreach ($hourList as $minutesSinceStartDateTime => $data) {
                if ($minutesSinceStartDateTime > (Carbon::now()->diffInMinutes($startDateTime2020))) {
                    unset($yearList[$year][$minutesSinceStartDateTime]);
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
                $dateTime->subMinutes(30);
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