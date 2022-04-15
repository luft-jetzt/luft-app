<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Entity\Station;
use App\Pollution\DataList\DataList;
use App\Pollution\DataRetriever\DataRetrieverInterface;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

abstract class AbstractPollutionDataFactory implements PollutionDataFactoryInterface
{
    protected CoordinateInterface $coord;

    protected MeasurementViewModelFactoryInterface $measurementViewModelFactory;

    protected DataList $dataList;

    protected DataRetrieverInterface $dataRetriever;

    protected PollutantFactoryStrategyInterface $strategy;

    public function __construct(MeasurementViewModelFactoryInterface $viewModelFactory, DataRetrieverInterface $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        $this->dataList = new DataList();
        $this->measurementViewModelFactory = $viewModelFactory;
        $this->dataRetriever = $dataRetriever;
        $this->strategy = $strategy;
    }

    public function setCoord(CoordinateInterface $coord): PollutionDataFactoryInterface
    {
        $this->coord = $coord;

        return $this;
    }

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

    public function setStrategy(PollutantFactoryStrategyInterface $strategy): PollutionDataFactoryInterface
    {
        $this->strategy = $strategy;

        return $this;
    }
}
