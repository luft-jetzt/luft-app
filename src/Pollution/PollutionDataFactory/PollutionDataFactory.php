<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Air\ViewModel\MeasurementViewModel;
use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\DataRetriever\DataRetrieverInterface;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Pollution\UniqueStrategy\Hasher;
use Doctrine\Persistence\ManagerRegistry;

class PollutionDataFactory extends AbstractPollutionDataFactory
{
    public function __construct(protected ManagerRegistry $managerRegistry, MeasurementViewModelFactoryInterface $measurementViewModelFactory, DataRetrieverInterface $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        parent::__construct($measurementViewModelFactory, $dataRetriever, $strategy);
    }

    public function createDecoratedPollutantList(\DateTime $dateTime = null, \DateInterval $dateInterval = null, int $workingSetSize = 20): array
    {
        if (!$dateTime) {
            $dateTime = new \DateTime();
        }

        if (!$dateInterval) {
            $dateInterval = new \DateInterval('PT12H');
        }

        $dateTime->sub($dateInterval);

        if ($this->coord instanceof Station) {
            $dataList = $this->managerRegistry->getRepository(Data::class)->findCurrentDataForStation($this->coord);
        } else {
            $dataList = $this->managerRegistry->getRepository(Data::class)->findCurrentDataForCoord($this->coord);
        }

        $measurementViewModelList = $this->getMeasurementViewModelListFromDataList($dataList);

        $measurementViewModelList = $this->decoratePollutantList($measurementViewModelList);

        return $measurementViewModelList;
    }

    protected function getMeasurementViewModelListFromDataList(array $dataList): array
    {
        $measurementViewModelList = [];

        /** @var Data $data */
        foreach ($dataList as $data) {
            $measurementViewModelList[$data->getPollutant()][Hasher::hashData($data)] = new MeasurementViewModel($data);
        }

        return $measurementViewModelList;
    }

    protected function decoratePollutantList(array $pollutantList): array
    {
        return $this
            ->reset()
            ->measurementViewModelFactory
            ->setCoord($this->coord)
            ->setPollutantList($pollutantList)
            ->decorate()
            ->getPollutantList()
        ;
    }
}
