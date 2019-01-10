<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\Box\Box;

class PollutionDataFactory extends AbstractPollutionDataFactory
{
    public function createDecoratedPollutantList(): array
    {
        $dateTime = new \DateTime();
        $interval = new \DateInterval('P3D');
        $dateTime->sub($interval);

        $dataList = $this->getDataListFromStationList($dateTime, $interval);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decoratePollutantList($boxList);

        return $boxList;
    }

    protected function getDataListFromStationList(\DateTime $fromDateTime = null, \DateInterval $interval = null): array
    {
        $this->dataList->reset();

        $missingPollutants = $this->strategy->getMissingPollutants($this->dataList);

        foreach ($missingPollutants as $pollutant) {
            $dataList = $this->dataRetriever->retrieveDataForCoord($this->coord, $pollutant, $fromDateTime, $interval);

            /** @var Data $data */
            foreach ($dataList as $data) {
                if ($this->strategy->accepts($this->dataList, $data)) {
                    $this->strategy->addDataToList($this->dataList, $data);
                }
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
