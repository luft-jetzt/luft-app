<?php declare(strict_types=1);

namespace App\Air\PollutionDataFactory;

use App\Air\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Air\ViewModelFactory\PollutantViewModelFactoryInterface;
use App\Entity\Data;
use App\Pollution\DataRetriever\HistoryElasticDataRetriever;
use App\Util\DateTimeUtil;
use Doctrine\Persistence\ManagerRegistry;

class HistoryDataFactory extends PollutionDataFactory implements HistoryDataFactoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry, PollutantViewModelFactoryInterface $viewModelFactory, HistoryElasticDataRetriever $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        parent::__construct($managerRegistry, $viewModelFactory, $dataRetriever, $strategy);
    }

    #[\Override]
    public function createDecoratedPollutantListForInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $this->getDataListsForInterval($fromDateTime, $untilDateTime);

        $dataLists = $this->convert();

        $pollutionModelList = [];

        /** @var array $dataList */
        foreach ($dataLists as $timestamp => $dataList) {
            $pollutantViewModelList = $this->getPollutantViewModelList($dataList);

            $pollutionModelList[$timestamp] = $this->decoratePollutantList($pollutantViewModelList);
        }

        return $pollutionModelList;
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
