<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\Measurement\PM10;
use App\Air\ViewModel\MeasurementViewModel;
use App\Air\ViewModelFactory\DistanceCalculator;
use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Analysis\CoronaFireworksAnalysis\Slot\YearSlot;
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
        $dataList = $this->valueFetcher->fetchValues($coord);

        /**
         * @var Data $data
         * @todo get timezone handling done!!! This is really nasty
         */
        foreach ($dataList as $data) {
            if ('ld' === $data->getProvider()) {
                /** @var YearSlot $yearSlot */
                foreach ($yearList as $yearSlot) {
                    if ($yearSlot->accepts($data)) {
                        $viewModel = $this->decorateData($data, $coord);

                        $yearSlot->addModel($viewModel);
                    }
                }
            }
        }

        /**
         * @todo quick fix to hide future values
         */
        /*foreach ($yearList as $year => $hourList) {
            foreach ($hourList as $minutesSinceStartDateTime => $data) {
                if ($minutesSinceStartDateTime > (Carbon::now()->diffInMinutes($startDateTime2020))) {
                    unset($yearList[$year][$minutesSinceStartDateTime]);
                }
            }
        }*/

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
            $yearList[$year->year] = new YearSlot($year->year);
            $year->subYear();
        }

        foreach ($yearList as $year => $hourList) {
            $startDateTime = StartDateTimeCalculator::calculateStartDateTime($year);
            $endDateTime = $startDateTime->copy()->addHours(24);

            $dateTime = $endDateTime->copy();

            do {
                $slot = $dateTime->diffInMinutes($startDateTime);

                $yearList[$year]->addSlot($slot);

                $dateTime->subMinutes(30);
            } while ($dateTime > $startDateTime);
        }

        return $yearList;
    }
}
