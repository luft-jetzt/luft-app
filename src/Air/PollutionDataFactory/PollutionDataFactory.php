<?php declare(strict_types=1);

namespace App\Air\PollutionDataFactory;

use App\Air\DataRetriever\DataRetrieverInterface;
use App\Air\Pollutant\PollutantInterface;
use App\Air\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Air\UniqueStrategy\Hasher;
use App\Air\ViewModel\PollutantViewModel;
use App\Air\ViewModelFactory\PollutantViewModelFactoryInterface;
use App\Entity\Data;
use Doctrine\Persistence\ManagerRegistry;

class PollutionDataFactory extends AbstractPollutionDataFactory
{
    public function __construct(protected ManagerRegistry $managerRegistry, PollutantViewModelFactoryInterface $pollutantViewModelFactory, DataRetrieverInterface $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        parent::__construct($pollutantViewModelFactory, $dataRetriever, $strategy);
    }

    #[\Override]
    public function createDecoratedPollutantList(\DateTime $dateTime = null, \DateInterval $dateInterval = null, int $workingSetSize = 20): array
    {
        if (!$dateTime) {
            $dateTime = new \DateTime();
        }

        if (!$dateInterval) {
            $dateInterval = new \DateInterval('PT12H');
        }

        $dateTime->sub($dateInterval);

        /**
        if ($this->coord instanceof Station) {
            $dataList = $this->managerRegistry->getRepository(Data::class)->findCurrentDataForStation($this->coord);
        } else {
            $dataList = $this->managerRegistry->getRepository(Data::class)->findCurrentDataForCoord($this->coord);
        }*/

        $dataList = $this->getDataListFromStationList($workingSetSize, $dateTime, $dateInterval);

        $pollutantViewModelList = $this->getPollutantViewModelList($dataList);

        $pollutantViewModelList = $this->decoratePollutantList($pollutantViewModelList);

        return $pollutantViewModelList;
    }

    protected function getPollutantViewModelList(array $dataList): array
    {
        $pollutantViewModelList = [];

        /** @var array $data */
        foreach ($dataList as $data) {
            /** @var Data $dataElement */
            foreach ($data as $dataElement) {
                if ($dataElement) {
                    $pollutant = $dataElement->getPollutant();

                    if ($pollutant === PollutantInterface::POLLUTANT_UVINDEXMAX) {
                        $pollutant = PollutantInterface::POLLUTANT_UVINDEX; // @todo this needs to be improved into a strategy
                    }

                    $pollutantViewModelList[$pollutant][Hasher::hashData($dataElement)] = new PollutantViewModel($dataElement);
                }
            }
        }

        return $pollutantViewModelList;
    }

    protected function decoratePollutantList(array $pollutantList): array
    {
        return $this
            ->reset()
            ->pollutantViewModelFactory
            ->setCoord($this->coord)
            ->setPollutantList($pollutantList)
            ->decorate()
            ->getPollutantList()
        ;
    }

    protected function getDataListFromStationList(int $workingSetSize, \DateTime $fromDateTime = null, \DateInterval $interval = null): array
    {
        $this->dataList->reset();

        $missingPollutants = $this->strategy->getMissingPollutants($this->dataList);

        foreach ($missingPollutants as $pollutantId) {
            $dataList = $this->dataRetriever->retrieveDataForCoord($this->coord, $pollutantId, $fromDateTime, $interval, 20.0, $workingSetSize);

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
}
