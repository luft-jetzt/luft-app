<?php declare(strict_types=1);

namespace App\Analysis\CoronaFireworksAnalysis;

use App\Pollution\PollutionDataFactory\PollutionDataFactoryInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class CoronaFireworksAnalysis implements CoronaFireworksAnalysisInterface
{
    protected PollutionDataFactoryInterface $pollutionDataFactory;

    public function __construct(PollutionDataFactoryInterface $pollutionDataFactory)
    {
        $this->pollutionDataFactory = $pollutionDataFactory;
    }

    public function analyze(CoordInterface $coord): array
    {
        $yearList = $this->initYearList();

        foreach ($yearList as $year => $hourList) {
            foreach ($hourList as $timestamp => $data) {
                $dateTime = Carbon::createFromTimestamp($timestamp);

                /*$result = $this->pollutionDataFactory
                    ->setCoord($coord)
                    ->createDecoratedPollutantList($dateTime, new CarbonInterval('PT30M'), 1)
                ;*/

                $result = null;

                $yearList[$year][$timestamp] = $result;
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
            $startDateTimeSpec = '%d-12-31 12:00:00';
            $startDateTime = new Carbon(sprintf($startDateTimeSpec, $year));
            $endDateTime = $startDateTime->copy()->addHours(36);

            $dateTime = $endDateTime->copy();

            do {
                $yearList[$year][$dateTime->format('Y-m-d-H-i-00')] = null;
                $dateTime->subMinutes(30);
            } while ($dateTime > $startDateTime);
        }

        return $yearList;
    }
}