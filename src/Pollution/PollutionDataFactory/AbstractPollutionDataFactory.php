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

    protected DataList $dataList;

    public function __construct(protected MeasurementViewModelFactoryInterface $measurementViewModelFactory, protected DataRetrieverInterface $dataRetriever, protected PollutantFactoryStrategyInterface $strategy)
    {
        $this->dataList = new DataList();
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
