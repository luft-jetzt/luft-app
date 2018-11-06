<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Data;
use App\Pollution\Box\Box;

class PollutionDataFactory extends AbstractPollutionDataFactory
{
    public function createDecoratedBoxList(): array
    {
        $dataList = $this->getDataListFromStationList($this->stationList);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $boxList;
    }

    protected function getDataListFromStationList(array $stationList): array
    {
        $this->dataList->reset();

        foreach ($stationList as $station) {
            foreach ($this->strategy->getMissingPollutants($this->dataList) as $pollutant) {
                $data = $this->dataRetriever->retrieveStationData($station, $pollutant);

                if ($this->strategy->accepts($data)) {
                    $this->strategy->addDataToList($this->dataList, $data);
                }
            }
        }

        return $this->dataList->getList();
    }

    protected function getBoxListFromDataList(array $dataList): array
    {
        $boxList = [];

        /** @var array $data */
        foreach ($dataList as $data) {
            /** @var Data $dataElement */
            foreach ($data as $dataElement) {
                if ($dataElement) {
                    $boxList[$dataElement->getPollutant()][$dataElement->getId()] = new Box($dataElement);
                }
            }
        }

        return $boxList;
    }

    protected function decorateBoxList(array $boxList): array
    {
        return $this
            ->reset()
            ->boxDecorator
            ->setBoxList($boxList)
            ->decorate()
            ->getBoxList()
        ;
    }
}
