<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\Box\Box;

class PollutionDataFactory extends AbstractPollutionDataFactory
{
    public function createDecoratedPollutantList(\DateTime $dateTime = null, \DateInterval $dateInterval = null): array
    {
        if (!$dateTime) {
            $dateTime = new \DateTime();
        }

        if (!$dateInterval) {
            $dateInterval = new \DateInterval('PT12H');
        }

        $dateTime->sub($dateInterval);

        $dataList = $this->getDataListFromStationList($dateTime, $dateInterval);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decoratePollutantList($boxList);

        return $boxList;
    }

    protected function getDataListFromStationList(\DateTime $fromDateTime = null, \DateInterval $interval = null): array
    {
        $this->dataList->reset();

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
