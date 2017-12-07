<?php

namespace AppBundle\Pollution\PollutionDataFactory;

use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\BoxDecorator\BoxDecoratorInterface;
use AppBundle\Pollution\DataList\DataList;
use AppBundle\Pollution\DataRetriever\DataRetrieverInterface;
use AppBundle\Pollution\StationFinder\StationFinderInterface;
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

        return $this;
    }

    public function createDecoratedBoxList(): array
    {
        $stationList = $this->stationFinder->setCoord($this->coord)->findNearestStations();

        $dataList = $this->getDataListFromStationList($stationList);

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
