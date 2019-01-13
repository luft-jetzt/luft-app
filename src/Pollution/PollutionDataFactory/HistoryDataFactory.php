<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Data;
use App\Util\DateTimeUtil;

class HistoryDataFactory extends PollutionDataFactory implements HistoryDataFactoryInterface
{
    public function createDecoratedPollutantListForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $dataLists = $this->getDataListsForInterval($fromDateTime, $untilDateTime);

        $dataLists = $this->convert($dataLists);

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

        $diffInterval = $fromDateTime->diff($untilDateTime);

        $this->dataList->reset();

        $missingPollutants = $this->strategy->getMissingPollutants($this->dataList);

        foreach ($missingPollutants as $pollutantId) {
            $dataListList[$pollutantId] = $this->dataRetriever->retrieveDataForCoord($this->coord, $pollutantId, $fromDateTime, $diffInterval);
        }

        return $dataListList;
    }

    protected function convert(array $dataListLists): array
    {
        $newDataListLists = [];

        foreach ($dataListLists as $pollutantId => $dataList) {
            /** @var Data $data */
            foreach ($dataList as $data) {
                $timestamp = DateTimeUtil::getHourStartDateTime($data->getDateTime())->format('U');

                if (!array_key_exists($timestamp, $newDataListLists)) {
                    $newDataListLists[$timestamp] = [$pollutantId => [$data]];
                } elseif (!array_key_exists($pollutantId, $newDataListLists[$timestamp])) {
                    $newDataListLists[$timestamp][$pollutantId] = [$data];
                } elseif ($newDataListLists[$timestamp][$pollutantId][0]->getValue() < $data->getValue()) {
                    $newDataListLists[$timestamp][$pollutantId][0] = $data;
                }
            }
        }

        return $newDataListLists;
    }
}


/**
 * $this->dataList->reset();

$missingPollutants = $this->strategy->getMissingPollutants($this->dataList);

foreach ($missingPollutants as $pollutantId) {
$dataList = $this->dataRetriever->retrieveDataForCoord($this->coord, $pollutantId, $fromDateTime, $interval);

if (0 === count($dataList)) {
continue;
}

while (!$this->strategy->isSatisfied($this->dataList, $pollutantId) && count($dataList)) {
$data = array_shift($dataList);

if ($this->strategy->accepts($this->dataList, $data)) {
$this->strategy->addDataToList($this->dataList, $data);
}
}
}

return $this->dataList->getList();
 */
