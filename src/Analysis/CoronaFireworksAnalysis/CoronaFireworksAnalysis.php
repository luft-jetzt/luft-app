<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\Measurement\PM10;
use App\Air\ViewModel\MeasurementViewModel;
use App\Air\ViewModelFactory\DistanceCalculator;
use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Entity\Data;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class CoronaFireworksAnalysis implements CoronaFireworksAnalysisInterface
{
    protected ValueFetcherInterface $valueFetcher;
    protected MeasurementViewModelFactoryInterface $measurementViewModelFactory;
    protected AirQualityCalculatorInterface $airQualityCalculator;

    public function __construct(ValueFetcherInterface $valueFetcher, MeasurementViewModelFactoryInterface $measurementViewModelFactory, AirQualityCalculatorInterface $airQualityCalculator)
    {
        $this->valueFetcher = $valueFetcher;
        $this->measurementViewModelFactory = $measurementViewModelFactory;
        $this->airQualityCalculator = $airQualityCalculator;
    }

    public function analyze(CoordInterface $coord): array
    {
        $yearList = $this->initYearList();

        foreach ($yearList as $year => $hourList) {
            $dataList = $this->valueFetcher->fetchValues($coord, $year);

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
        $startDateTime2020 = new Carbon('2020-12-31 12:00:00', new CarbonTimeZone('UTC'));

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
            $endDateTime = $startDateTime->copy()->addHours(24);

            $dateTime = $endDateTime->copy();

            do {
                $yearList[$year][$dateTime->diffInMinutes($startDateTime)] = null;
                $dateTime->subMinutes(30);
            } while ($dateTime > $startDateTime);
        }

        return $yearList;
    }


}