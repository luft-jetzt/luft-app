<?php declare(strict_types=1);

namespace App\Air\PollutionDataFactory;

use App\Air\DataList\DataList;
use App\Air\DataRetriever\DataRetrieverInterface;
use App\Air\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Entity\Station;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

abstract class AbstractPollutionDataFactory implements PollutionDataFactoryInterface
{
    protected CoordinateInterface $coord;

    protected DataList $dataList;

    public function __construct(protected MeasurementViewModelFactoryInterface $measurementViewModelFactory, protected DataRetrieverInterface $dataRetriever, protected PollutantFactoryStrategyInterface $strategy)
    {
        $this->dataList = new DataList();
    }

    #[\Override]
    public function setCoord(CoordinateInterface $coord): PollutionDataFactoryInterface
    {
        $this->coord = $coord;

        return $this;
    }

    #[\Override]
    public function setStation(Station $station): PollutionDataFactoryInterface
    {
        $this->coord = $station;

        return $this;
    }

    protected function reset(): AbstractPollutionDataFactory
    {
        $this->dataList->reset();

        return $this;
    }

    #[\Override]
    public function setStrategy(PollutantFactoryStrategyInterface $strategy): PollutionDataFactoryInterface
    {
        $this->strategy = $strategy;

        return $this;
    }
}
