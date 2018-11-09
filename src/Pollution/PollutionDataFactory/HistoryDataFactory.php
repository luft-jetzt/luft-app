<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

class HistoryDataFactory extends PollutionDataFactory
{
    public function getDataListsForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $dataListList = [];

        $hour = new \DateInterval('PT1H');
        $dateTime = clone $fromDateTime;

        while ($dateTime < $untilDateTime) {
            $dataListList[$dateTime->format('U')] = $this->getDataListFromStationList($this->stationList, $dateTime, $hour, 'ASC');

            $dateTime->add($hour);
        }

        return $dataListList;
    }
}
