<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Data;
use App\Entity\Station;
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

        /** @var Station $station */
        foreach ($stationList as $station) {
            echo "STATION: ".$station->getStationCode()."<br />";
            foreach ($this->strategy->getMissingPollutants($this->dataList) as $pollutant) {
                echo "POLLUTANT: ".$pollutant."<br />";

                $data = $this->dataRetriever->retrieveStationData($station, $pollutant);

                var_dump($data !== null);
                if ($this->strategy->accepts($this->dataList, $data)) {
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
