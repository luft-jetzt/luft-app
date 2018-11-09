<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\Box\Box;

class PollutionDataFactory extends AbstractPollutionDataFactory
{
    public function createDecoratedPollutantList(): array
    {
        $dataList = $this->getDataListFromStationList($this->stationList);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decoratePollutantList($boxList);

        return $boxList;
    }

    protected function getDataListFromStationList(array $stationList): array
    {
        $this->dataList->reset();

        $missingPollutants = $this->strategy->getMissingPollutants($this->dataList);

        /** @var Station $station */
        foreach ($stationList as $station) {
            foreach ($missingPollutants as $pollutant) {
                $data = $this->dataRetriever->retrieveStationData($station, $pollutant);

                if ($this->strategy->accepts($this->dataList, $data)) {
                    $this->strategy->addDataToList($this->dataList, $data);
                }
            }

            $missingPollutants = $this->strategy->getMissingPollutants($this->dataList);

            if (0 === count($missingPollutants)) {
                break;
            }
        }

        return $this->dataList->getList();
    }

    protected function getBoxListFromDataList(array $dataList): array
    {
        $pollutantList = [];

        /** @var array $data */
        foreach ($dataList as $data) {
            /** @var Data $dataElement */
            foreach ($data as $dataElement) {
                if ($dataElement) {
                    $pollutantList[$dataElement->getPollutant()][$dataElement->getId()] = new Box($dataElement);
                }
            }
        }

        return $pollutantList;
    }

    protected function decoratePollutantList(array $pollutantList): array
    {
        return $this
            ->reset()
            ->boxDecorator
            ->setCoord($this->coord)
            ->setPollutantList($pollutantList)
            ->decorate()
            ->getPollutantList()
        ;
    }
}
