<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Station;
use App\Pollution\Box\Box;
use App\Pollution\BoxDecorator\BoxDecoratorInterface;
use App\Pollution\DataList\DataList;
use App\Pollution\DataRetriever\DataRetrieverInterface;
use App\Pollution\StationFinder\StationFinderInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

class PollutionDataFactory
{
    /** @var CoordInterface $coord */
    protected $coord;

    /** @var StationFinderInterface $stationFinder */
    protected $stationFinder;

    /** @var StationFinderInterface $boxDecorator */
    protected $boxDecorator;

    /** @var DataList $dataList */
    protected $dataList;

    /** @var DataRetrieverInterface */
    protected $dataRetriever;

    protected $stationList = [];

    public function __construct(StationFinderInterface $stationFinder, BoxDecoratorInterface $boxDecorator, DataRetrieverInterface $dataRetriever)
    {
        $this->stationFinder = $stationFinder;
        $this->dataList = new DataList();
        $this->boxDecorator = $boxDecorator;
        $this->dataRetriever = $dataRetriever;
    }

    public function setCoord(CoordInterface $coord): PollutionDataFactory
    {
        $this->coord = $coord;
        $this->stationList = $this->stationFinder->setCoord($this->coord)->findNearestStations();

        return $this;
    }

    public function setStation(Station $station): PollutionDataFactory
    {
        $this->coord = $station;
        $this->stationList = [$station];

        return $this;
    }

    public function createDecoratedBoxList(): array
    {
        $dataList = $this->getDataListFromStationList($this->stationList);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $boxList;
    }

    protected function getDataListFromStationList(array $stationList): array
    {
        $this->dataList->reset();

        foreach ($stationList as $station) {
            foreach ($this->dataList->getMissingPollutants() as $pollutant) {
                $data = $this->dataRetriever->retrieveStationData($station, $pollutant);

                if ($data) {
                    $this->dataList->addData($data);
                }
            }
        }

        return $this->dataList->getList();
    }

    protected function getBoxListFromDataList(array $dataList): array
    {
        $boxList = [];

        foreach ($dataList as $data) {
            if ($data) {
                $boxList[] = new Box($data);
            }
        }

        return $boxList;
    }

    protected function decorateBoxList(array $boxList): array
    {
        return $this
            ->reset()
            ->boxDecorator
            ->setBoxList($boxList)
            ->decorate()
            ->getBoxList()
        ;
    }

    protected function reset(): PollutionDataFactory
    {
        $this->dataList->reset();

        return $this;
    }
}
