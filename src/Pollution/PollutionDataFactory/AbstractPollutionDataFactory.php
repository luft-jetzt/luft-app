<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Air\ViewModelFactory\MeasurementViewModelFactoryInterface;
use App\Entity\Station;
use App\Pollution\DataList\DataList;
use App\Pollution\DataRetriever\DataRetrieverInterface;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Pollution\StationFinder\StationFinderInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

abstract class AbstractPollutionDataFactory implements PollutionDataFactoryInterface
{
    /** @var CoordInterface $coord */
    protected $coord;

    /** @var StationFinderInterface $stationFinder */
    protected $stationFinder;

    /** @var MeasurementViewModelFactoryInterface $viewModelFactory */
    protected $measurementViewModelFactory;

    /** @var DataList $dataList */
    protected $dataList;

    /** @var DataRetrieverInterface $dataRetriever*/
    protected $dataRetriever;

    /** @var PollutantFactoryStrategyInterface $strategy */
    protected $strategy;

    public function __construct(StationFinderInterface $stationFinder, MeasurementViewModelFactoryInterface $viewModelFactory, DataRetrieverInterface $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        $this->stationFinder = $stationFinder;
        $this->dataList = new DataList();
        $this->measurementViewModelFactory = $viewModelFactory;
        $this->dataRetriever = $dataRetriever;
        $this->strategy = $strategy;
    }

    public function setCoord(CoordInterface $coord): PollutionDataFactoryInterface
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
