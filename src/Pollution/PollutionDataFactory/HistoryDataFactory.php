<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Entity\Data;
use App\Pollution\DataList\DataList;
use App\Pollution\DataRetriever\HistoryElasticDataRetriever;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Util\DateTimeUtil;
use Doctrine\Persistence\ManagerRegistry;

class HistoryDataFactory extends PollutionDataFactory implements HistoryDataFactoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry, MeasurementViewModelFactoryInterface $viewModelFactory, HistoryElasticDataRetriever $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        parent::__construct($managerRegistry, $viewModelFactory, $dataRetriever, $strategy);
    }

    public function createDecoratedPollutantListForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $this->getDataListsForInterval($fromDateTime, $untilDateTime);

        $dataLists = $this->convert();

        $measurementModelLists = [];

        /** @var array $dataList */
        foreach ($dataLists as $timestamp => $dataList) {
            $measurementModelList = $this->getMeasurementViewModelListFromDataList($dataList);

            $measurementModelLists[$timestamp] = $this->decoratePollutantList($measurementModelList);
        }

        return $measurementModelLists;
    }

    public function getDataListsForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): void
    {
        $this->dataList->reset();

        $diffInterval = $fromDateTime->diff($untilDateTime);

        $resultList = $this->dataRetriever->retrieveDataForCoord($this->coord, null, $fromDateTime, $diffInterval);

        /** @var Data $data */
        foreach ($resultList as $data) {
            $this->dataList->addData($data);
        }
    }

    protected function convert(): array
    {
        $newDataListLists = [];

        foreach ($this->dataList->getList() as $pollutantId => $dataList) {
            /**
             * @var Data $data
             */
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
