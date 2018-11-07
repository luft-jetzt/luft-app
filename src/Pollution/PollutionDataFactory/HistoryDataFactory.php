<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

class HistoryDataFactory extends PollutionDataFactory
{
    public function getDataListsForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $hour = new \DateInterval('PT1H');
        $dateTime = clone $fromDateTime;

        while ($dateTime < $untilDateTime) {
            $this->getDataListFromStationList($this->stationList, $dateTime, $hour);

            $dateTime->add($hour);
        }

    }

}
