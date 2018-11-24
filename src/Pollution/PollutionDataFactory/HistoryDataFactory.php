<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

class HistoryDataFactory extends PollutionDataFactory implements HistoryDataFactoryInterface
{
    public function createDecoratedPollutantListForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $dataLists = $this->getDataListsForInterval($fromDateTime, $untilDateTime);

        $boxLists = [];

        /** @var array $dataList */
        foreach ($dataLists as $timestamp => $dataList) {
            $boxList = $this->getBoxListFromDataList($dataList);
            $boxLists[$timestamp] = $this->decoratePollutantList($boxList);
        }

        return $boxLists;
    }

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
