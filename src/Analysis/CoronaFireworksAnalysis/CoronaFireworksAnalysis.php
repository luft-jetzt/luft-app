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

class CoronaFireworksAnalysis implements CoronaFireworksAnalysisInterface
{
    public function __construct(protected ValueFetcherInterface $valueFetcher, protected MeasurementViewModelFactoryInterface $measurementViewModelFactory, protected AirQualityCalculatorInterface $airQualityCalculator)
    {
    }

    public function analyze(CoordInterface $coord): array
    {
        $yearList = $this->initYearList();
        $dataList = $this->valueFetcher->fetchValues($coord, array_keys($yearList), 18);

        /**
         * @var Data $data
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

        $startDateTime = StartDateTimeCalculator::calculateStartDateTime(2021);
        $diff = Carbon::now()->diffInMinutes($startDateTime);

        /**
         * @var int $year
         * @var YearSlot $slotList
         */
        foreach ($yearList as $year => $yearSlot) {
            foreach ($yearSlot->getList() as $minutesSinceStartDateTime => $data) {
                if (Carbon::now() < $startDateTime || $minutesSinceStartDateTime > $diff) {
                    $yearSlot->removeSlot($minutesSinceStartDateTime);
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

        for ($yearSub = 0; $yearSub <= 3; ++$yearSub) {
            $yearList[$year->year] = new YearSlot($year->year);
            $year->subYear();
        }

        foreach ($yearList as $year => $hourList) {
            $startDateTime = StartDateTimeCalculator::calculateStartDateTime($year);
            $endDateTime = $startDateTime->copy()->addHours(12);

            $dateTime = $endDateTime->copy();

            do {
                $slot = $dateTime->diffInMinutes($startDateTime);

                $yearList[$year]->addSlot($slot);

                $dateTime->subHour();
            } while ($dateTime >= $startDateTime);
        }

        return $yearList;
    }
}
