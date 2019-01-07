<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Station;
use App\Pollution\Box\Box;
use App\Pollution\BoxDecorator\BoxDecoratorInterface;
use App\Pollution\DataList\DataList;
use App\Pollution\DataRetriever\DataRetrieverInterface;
use App\Pollution\DataRetriever\TweakedElasticDataRetriever;
use App\Pollution\DataRetriever\TweakedElasticDataRetrieverInterface;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Pollution\StationFinder\StationFinderInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

abstract class AbstractPollutionDataFactory implements PollutionDataFactoryInterface
{
    /** @var CoordInterface $coord */
    protected $coord;

    /** @var StationFinderInterface $stationFinder */
    protected $stationFinder;

    /** @var StationFinderInterface $boxDecorator */
    protected $boxDecorator;

    /** @var DataList $dataList */
    protected $dataList;

    /** @var TweakedElasticDataRetrieverInterface $dataRetriever*/
    protected $dataRetriever;

    /** @var array $stationList */
    protected $stationList = [];

    /** @var PollutantFactoryStrategyInterface $strategy */
    protected $strategy;

    public function __construct(StationFinderInterface $stationFinder, BoxDecoratorInterface $boxDecorator, TweakedElasticDataRetrieverInterface $dataRetriever, PollutantFactoryStrategyInterface $strategy)
    {
        $this->stationFinder = $stationFinder;
        $this->dataList = new DataList();
        $this->boxDecorator = $boxDecorator;
        $this->dataRetriever = $dataRetriever;
        $this->strategy = $strategy;
    }

    public function setCoord(CoordInterface $coord): PollutionDataFactoryInterface
    {
        $this->coord = $coord;
        $this->stationList = $this->stationFinder->setCoord($this->coord)->findNearestStations();

        return $this;
    }

    public function setStation(Station $station): PollutionDataFactoryInterface
    {
        $this->coord = $station;
        $this->stationList = [$station];

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
